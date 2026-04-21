<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden pb-5">
            <header class="mb-5">
                <h1 class="display-5 fw-black text-dark mb-2">Planificación Financiera</h1>
                <p class="text-muted fw-bold small text-uppercase tracking-widest">Simulación de proyecciones y gestión de liquidez</p>
            </header>

            <!-- Key Projections Grid -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">Patrimonio Total</h6>
                        <div class="h2 fw-black text-primary mb-0">
                            {{ $privacy.enabled ? '****' : formatCurrency(aggregated.current_balance) }}
                        </div>
                        <div class="small text-muted mt-2">Capital Actual</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white border-start border-4 border-info">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">A 1 Año</h6>
                        <div class="h2 fw-black text-dark mb-0">
                            {{ $privacy.enabled ? '****' : formatCurrency(aggregated.projected_1y) }}
                        </div>
                        <div class="small text-success fw-bold mt-2">
                             <span v-if="!$privacy.enabled">+{{ formatCurrency(aggregated.projected_1y - aggregated.current_balance) }}</span>
                             <span v-else>****</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white border-start border-4 border-primary">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">A 5 Años</h6>
                        <div class="h2 fw-black text-dark mb-0">
                            {{ $privacy.enabled ? '****' : formatCurrency(aggregated.projected_5y) }}
                        </div>
                        <div class="small text-success fw-bold mt-2">
                             <span v-if="!$privacy.enabled">+{{ formatCurrency(aggregated.projected_5y - aggregated.current_balance) }}</span>
                             <span v-else>****</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white border-start border-4 border-indigo">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">A 10 Años</h6>
                        <div class="h2 fw-black text-dark mb-0">
                            {{ $privacy.enabled ? '****' : formatCurrency(aggregated.projected_10y) }}
                        </div>
                        <div class="small text-success fw-bold mt-2">
                             <span v-if="!$privacy.enabled">+{{ formatCurrency(aggregated.projected_10y - aggregated.current_balance) }}</span>
                             <span v-else>****</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 bg-white">
                <h5 class="fw-black text-dark mb-4 text-uppercase small tracking-widest border-bottom pb-3">
                    Configuración de Escenarios
                </h5>
                <form @submit.prevent="saveSettings" class="row g-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small uppercase tracking-tighter">Retorno esperado Inversiones (%)</label>
                        <input type="number" step="0.01" class="form-control bg-light border-0 p-3 rounded-3" v-model="formSettings.investment_return_rate">
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" v-model="formSettings.enable_tax_projection" id="taxSwitch">
                            <label class="form-check-label fw-bold small text-muted" for="taxSwitch">Simular impuestos sobre beneficio</label>
                        </div>
                        <input v-if="formSettings.enable_tax_projection" type="number" step="0.01" class="form-control bg-light border-0 p-3 rounded-3" v-model="formSettings.tax_rate" placeholder="Tipo impositivo (21%)">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm" :disabled="savingSettings">
                            {{ savingSettings ? 'Guardando...' : 'Actualizar Proyecciones' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bank Accounts -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-black text-dark mb-0 text-uppercase small tracking-widest">Cuentas y Depósitos</h5>
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#accountModal" @click="resetForm">
                        <i class="bi bi-plus-lg me-2"></i> Añadir
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-0">
                        <thead class="bg-light fs-xs text-muted fw-bold uppercase">
                            <tr>
                                <th class="border-0 px-4">Cuenta</th>
                                <th class="border-0">Tipo</th>
                                <th class="border-0 text-end">Saldo</th>
                                <th class="border-0 text-end">APY %</th>
                                <th class="border-0 text-end">Interés Mensual</th>
                                <th class="border-0 text-end px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="account in projections" :key="account.id">
                                <td class="px-4 fw-bold text-dark">{{ account.name }}</td>
                                <td>
                                    <span class="badge rounded-pill fw-bold bg-opacity-10" 
                                          :class="account.apy > 0 ? 'bg-success text-success' : 'bg-primary text-primary'" style="font-size: 10px;">
                                        {{ account.apy > 0 ? 'Remunerada' : 'Corriente' }}
                                    </span>
                                </td>
                                <td class="text-end fw-black">
                                    {{ $privacy.enabled ? '****' : formatCurrency(account.current_balance) }}
                                </td>
                                <td class="text-end fw-bold text-muted">{{ account.apy }}%</td>
                                <td class="text-end fw-bold text-success">
                                    <span v-if="!$privacy.enabled">+{{ formatCurrency(account.monthly_earnings) }}</span>
                                    <span v-else>****</span>
                                </td>
                                <td class="text-end px-4">
                                    <button class="btn btn-light btn-sm rounded-circle me-1" @click="editAccount(account.id)"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-light btn-sm rounded-circle text-danger" @click="deleteAccount(account.id)"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr v-if="projections.length === 0">
                                <td colspan="6" class="text-center py-5 text-muted small italic">No hay cuentas bancarias registradas</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ACCOUNT MODAL -->
        <div class="modal fade" id="accountModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4 p-4">
                    <div class="modal-header border-0 pb-0">
                        <h4 class="fw-black text-dark mb-0">{{ editingId ? 'EDITAR CUENTA' : 'NUEVA CUENTA' }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <form @submit.prevent="submitAccount">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small uppercase tracking-tighter">Nombre de la Cuenta</label>
                                <input type="text" class="form-control rounded-3 p-3 bg-light border-0" v-model="formAccount.name" placeholder="Ej: Ahorro Libertad" required>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small uppercase tracking-tighter">Saldo Actual</label>
                                    <input type="number" step="0.01" class="form-control rounded-3 p-3 bg-light border-0" v-model="formAccount.balance" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small uppercase tracking-tighter">Interés APY (%)</label>
                                    <input type="number" step="0.01" class="form-control rounded-3 p-3 bg-light border-0" v-model="formAccount.apy" placeholder="2.50">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm" :disabled="submitting">
                                {{ submitting ? 'Guardando...' : (editingId ? 'ACTUALIZAR' : 'GUARDAR CUENTA') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'FinancialPlanningIndex',
    props: {
        bankAccounts: Array,
        projections: Array,
        aggregated: Object,
        settings: Object
    },
    data() {
        return {
            savingSettings: false,
            submitting: false,
            editingId: null,
            formSettings: {
                investment_return_rate: this.settings.investment_return_rate,
                tax_rate: this.settings.tax_rate,
                enable_tax_projection: this.settings.enable_tax_projection
            },
            formAccount: {
                name: '',
                balance: '',
                apy: '',
                type: 'savings',
                currency: 'EUR'
            }
        }
    },
    methods: {
        formatCurrency(value) {
            return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
        },
        async saveSettings() {
            this.savingSettings = true;
            try {
                await axios.post('/financial-planning/settings', this.formSettings);
                window.location.reload();
            } catch (e) {
                alert('Error al guardar ajustes.');
            } finally {
                this.savingSettings = false;
            }
        },
        resetForm() {
            this.editingId = null;
            this.formAccount = { name: '', balance: '', apy: '', type: 'savings', currency: 'EUR' };
        },
        editAccount(id) {
            const acc = this.bankAccounts.find(a => a.id === id);
            if (acc) {
                this.editingId = id;
                this.formAccount = { ...acc };
                const modal = new bootstrap.Modal(document.getElementById('accountModal'));
                modal.show();
            }
        },
        async submitAccount() {
            this.submitting = true;
            try {
                if (this.editingId) {
                    await axios.put(`/bank-accounts/${this.editingId}`, this.formAccount);
                } else {
                    await axios.post('/bank-accounts', this.formAccount);
                }
                window.location.reload();
            } catch (e) {
                alert('Error al procesar la cuenta.');
            } finally {
                this.submitting = false;
            }
        },
        async deleteAccount(id) {
            if (confirm('¿Eliminar esta cuenta?')) {
                try {
                    await axios.delete(`/bank-accounts/${id}`);
                    window.location.reload();
                } catch (e) {
                    alert('Error al eliminar.');
                }
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1.25rem !important; }
.border-indigo { border-color: #6366f1 !important; }
.bg-indigo { background-color: #6366f1 !important; }
.fs-xs { font-size: 0.65rem; }
.shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
</style>
