import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { useTransactionCalculator } from './useTransactionCalculator';

export function useTransactionForm(props, emit) {
    const form = useForm({
        type: 'expense',
        amount: '',
        date: new Date().toISOString().substring(0, 10),
        time: '',
        category_id: null,
        category_name: '',
        description: '',
        asset_name: '',
        asset_full_name: '',
        asset_type: 'stock',
        market_asset_id: null,
        isin: '',
        quantity: '',
        price_per_unit: '',
        portfolio_id: '',
        fees: '',
        exchange_fees: '',
        tax: '',
        currency_code: 'EUR',
    });

    const isFetchingPrice = ref(false);
    const priceSource = ref(null);
    const lastEditedField = ref('amount');
    const isFormLoading = ref(false);

    // Integrar calculadora
    const { initWatchers } = useTransactionCalculator(form, lastEditedField);
    initWatchers();

    const fetchPrice = async () => {
        if ((!form.asset_name && !form.market_asset_id) || !form.date || isFormLoading.value) return;
        
        isFetchingPrice.value = true;
        priceSource.value = null;

        try {
            const params = { date: form.date, type: form.asset_type };
            if (form.market_asset_id) params.market_asset_id = form.market_asset_id;
            else params.ticker = form.asset_name;

            const { data } = await axios.get(route('market.price'), { params });
            
            if (data.price) {
                form.price_per_unit = data.price;
                priceSource.value = `${data.source} (${data.currency})`;
                if (data.currency) form.currency_code = data.currency;
            }
        } catch (e) {
            if (e.response?.status !== 404) console.error('Error de precio:', e);
        } finally {
            isFetchingPrice.value = false;
        }
    };

    const initForm = () => {
        isFormLoading.value = true;
        priceSource.value = null;

        if (props.transaction?.id) {
            // MODO EDICIÓN
            Object.keys(form.data()).forEach(key => {
                if (key === 'date' && props.transaction.date) {
                    form.date = String(props.transaction.date).substring(0, 10);
                } else if (key === 'time' && props.transaction.time) {
                    form.time = String(props.transaction.time).substring(0, 5);
                } else if (key === 'category_name') {
                    form.category_name = typeof props.transaction.category === 'object' 
                        ? props.transaction.category?.name 
                        : (props.transaction.category || '');
                } else if (props.transaction[key] !== undefined) {
                    form[key] = props.transaction[key];
                }
            });
            
            if (props.transaction.asset) {
                form.asset_name = props.transaction.asset.ticker;
                form.asset_full_name = props.transaction.asset.name;
                form.asset_type = props.transaction.asset.type;
                form.market_asset_id = props.transaction.asset.market_asset_id;
            }
        } else {
            // MODO CREACIÓN
            form.reset();
            form.date = new Date().toISOString().substring(0, 10);
            if (props.defaultPortfolioId && props.defaultPortfolioId !== 'aggregated') {
                form.portfolio_id = props.defaultPortfolioId;
            } else if (props.portfolios?.length > 0) {
                form.portfolio_id = props.portfolios[0].id;
            }
        }

        setTimeout(() => isFormLoading.value = false, 150);
    };

    const submit = () => {
        if (form.processing) return;
        const routeName = props.transaction?.id ? 'transactions.update' : 'transactions.store';
        const method = props.transaction?.id ? 'put' : 'post';
        const routeParams = props.transaction?.id ? props.transaction.id : [];
        
        form[method](route(routeName, routeParams), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    };

    const confirmDelete = () => {
        if (!props.transaction?.id) return;
        form.delete(route('transactions.destroy', props.transaction.id), {
            onSuccess: () => emit('close'),
        });
    };

    // Watchers de inicialización
    watch(() => props.show, (show) => { if (show) initForm(); });
    watch(() => [form.date, form.market_asset_id], () => { if (!isFormLoading.value) fetchPrice(); });

    return {
        form,
        isFetchingPrice,
        priceSource,
        lastEditedField,
        isFormLoading,
        initForm,
        fetchPrice,
        submit,
        confirmDelete
    };
}
