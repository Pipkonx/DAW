<template>
    <div v-if="assets.length > 0" class="card border-0 border-start border-4 border-warning shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center cursor-pointer" @click="isExpanded = !isExpanded">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.2rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">
                        Activos Pendientes de Vinculación ({{ assets.length }})
                    </h5>
                </div>
                <button class="btn btn-link text-muted p-0">
                    <i class="bi" :class="isExpanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </button>
            </div>
            
            <div v-if="isExpanded" class="mt-4 animate-fade-in">
                <p class="small text-muted mb-4 fw-bold">
                    Los siguientes activos fueron detectados pero no se pudieron vincular automáticamente con datos de mercado. 
                    Por favor, verifique el nombre o asigne un ISIN manualmente para obtener precios actualizados.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-2 text-muted fw-black uppercase border-0" style="font-size: 0.65rem;">Nombre Detectado</th>
                                <th class="py-2 text-muted fw-black uppercase border-0" style="font-size: 0.65rem;">Original (OCR)</th>
                                <th class="py-2 text-muted fw-black uppercase border-0" style="font-size: 0.65rem;">Estado</th>
                                <th class="pe-4 py-2 text-end text-muted fw-black uppercase border-0" style="font-size: 0.65rem;">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="asset in assets" :key="asset.id">
                                <td class="ps-4 py-3 fw-bold text-dark">
                                    {{ asset.name }}
                                </td>
                                <td class="py-3 text-muted">
                                    {{ asset.original_name || '-' }}
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 font-black" style="font-size: 0.6rem;">
                                        {{ asset.link_status === 'pending' ? 'PENDIENTE' : 'FALLIDO' }}
                                    </span>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <button @click="resolveAsset(asset)" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-black" style="font-size: 0.7rem;">
                                        GESTIONAR
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'UnlinkedAssetsLog',
    props: {
        assets: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            isExpanded: true
        }
    },
    methods: {
        resolveAsset(asset) {
            // Navegación directa en MPA
            window.location.href = `/transactions?asset_id=${asset.id}`;
        }
    }
}
</script>

<style scoped>
.rounded-4 { border-radius: 1.25rem !important; }
.fw-black { font-weight: 900; }
.uppercase { text-transform: uppercase; }
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
