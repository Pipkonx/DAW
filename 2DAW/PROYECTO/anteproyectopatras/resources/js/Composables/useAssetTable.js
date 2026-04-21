import { ref, computed } from 'vue';

export function useAssetTable(assets) {
    const bulkSelectedAssets = ref([]);
    const assetFilter = ref('');
    const selectedPeriod = ref('all');

    const periods = [
        { value: '1d', label: '1D' },
        { value: '1w', label: '1S' },
        { value: '1m', label: '1M' },
        { value: 'ytd', label: 'YTD' },
        { value: '1y', label: '1A' },
        { value: 'all', label: 'Max' }
    ];

    const filteredAssets = computed(() => {
        if (!assetFilter.value) return assets.value;
        const lower = assetFilter.value.toLowerCase();
        return assets.value.filter(a => 
            a.name.toLowerCase().includes(lower) || 
            a.ticker.toLowerCase().includes(lower)
        );
    });

    const toggleBulkSelection = (id) => {
        if (bulkSelectedAssets.value.includes(id)) {
            bulkSelectedAssets.value = bulkSelectedAssets.value.filter(aId => aId !== id);
        } else {
            bulkSelectedAssets.value.push(id);
        }
    };

    const toggleAllBulk = () => {
        const allIds = filteredAssets.value.map(a => a.id);
        const allSelected = allIds.length > 0 && allIds.every(id => bulkSelectedAssets.value.includes(id));
        
        if (allSelected) {
            bulkSelectedAssets.value = [];
        } else {
            bulkSelectedAssets.value = allIds;
        }
    };

    const getAssetPerformance = (asset) => {
        if (selectedPeriod.value === 'all') {
            return {
                value: asset.profit_loss,
                percentage: asset.profit_loss_percentage
            };
        }
        // Placeholder for historical performance
        return {
            value: asset.profit_loss,
            percentage: asset.profit_loss_percentage
        };
    };

    return {
        bulkSelectedAssets,
        assetFilter,
        selectedPeriod,
        periods,
        filteredAssets,
        toggleBulkSelection,
        toggleAllBulk,
        getAssetPerformance
    };
}
