<template>
    <div class="py-4 bg-light min-vh-100">
        <div class="container">
            <div class="row g-4">
                <!-- COLUMNA IZQUIERDA: Perfil y Sugerencias -->
                <aside class="col-lg-3 d-none d-lg-block">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-center p-4">
                        <div class="mb-3">
                            <img :src="'https://ui-avatars.com/api/?name=' + user.name" class="rounded-circle shadow-sm" width="80" height="80">
                        </div>
                        <h5 class="fw-bold mb-0">{{ user.name }}</h5>
                        <p class="text-muted small">Inversor Pipkonx</p>
                        <hr class="my-3">
                        <div class="row small">
                            <div class="col-6 border-end">
                                <div class="fw-bold">128</div>
                                <div class="text-muted text-uppercase" style="font-size: 10px;">Seguidores</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold">45</div>
                                <div class="text-muted text-uppercase" style="font-size: 10px;">Siguiendo</div>
                            </div>
                        </div>
                    </div>

                    <!-- Gurus -->
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3 opacity-75">Gurus del Mercado</h6>
                        <div v-for="guru in famousPortfolios" :key="guru.slug" class="d-flex align-items-center mb-3">
                            <img :src="guru.avatar" class="rounded-circle me-3" width="32" height="32">
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-bold small text-truncate">{{ guru.name }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ guru.desc }}</div>
                            </div>
                            <div class="small fw-bold" :class="guru.change >= 0 ? 'text-success' : 'text-danger'">
                                {{ guru.change >= 0 ? '+' : '' }}{{ guru.change }}%
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- COLUMNA CENTRAL: Feed -->
                <main class="col-lg-6">
                    <!-- Crear Publicación -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="d-flex gap-3">
                            <img :src="'https://ui-avatars.com/api/?name=' + user.name" class="rounded-circle" width="40" height="40">
                            <div class="flex-grow-1">
                                <textarea 
                                    v-model="newPost.content" 
                                    class="form-control border-0 bg-light rounded-3 p-3 mb-3" 
                                    placeholder="¿Qué estás analizando hoy?"
                                    rows="3"
                                ></textarea>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-sm btn-light rounded-pill px-3">
                                        <i class="bi bi-image me-1"></i> Imagen
                                    </button>
                                    <button 
                                        class="btn btn-primary rounded-pill px-4" 
                                        :disabled="!newPost.content"
                                        @click="submitPost"
                                    >
                                        Publicar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="d-flex gap-2 mb-4">
                        <button 
                            v-for="f in ['recent', 'trending', 'following']" 
                            :key="f"
                            class="btn btn-sm rounded-pill px-3 fw-bold text-capitalize"
                            :class="activeFilter === f ? 'btn-primary' : 'btn-white shadow-sm border-0'"
                            @click="switchFilter(f)"
                        >
                            {{ f === 'recent' ? 'Recientes' : (f === 'trending' ? 'Tendencias' : 'Siguiendo') }}
                        </button>
                    </div>

                    <!-- Listado de Posts -->
                    <div v-if="posts.data.length === 0" class="text-center py-5">
                        <div class="display-1 text-muted opacity-25 mb-3"><i class="bi bi-newspaper"></i></div>
                        <h5>Muro en silencio</h5>
                        <p class="text-muted">¡Sé el primero en compartir algo!</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="post in posts.data" :key="post.id" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                            <div class="card-body p-4">
                                <!-- Cabecera Post -->
                                <div class="d-flex align-items-center mb-3">
                                    <img :src="'https://ui-avatars.com/api/?name=' + post.user.name" class="rounded-circle me-3" width="45" height="45">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark mb-0">{{ post.user.name }}</div>
                                        <div class="text-muted small">{{ post.created_at }}</div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li><a class="dropdown-item small" href="#">Reportar</a></li>
                                            <li v-if="post.user_id === user.id"><hr class="dropdown-divider"></li>
                                            <li v-if="post.user_id === user.id"><a class="dropdown-item small text-danger" @click="deletePost(post.id)">Eliminar</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Contenido -->
                                <p class="card-text mb-3" style="white-space: pre-wrap;">{{ post.content }}</p>
                                
                                <div v-if="post.image_path" class="mb-3 rounded-4 overflow-hidden border">
                                    <img :src="'/storage/' + post.image_path" class="img-fluid w-100">
                                </div>

                                <!-- Acciones -->
                                <div class="d-flex gap-4 pt-2 border-top">
                                    <button class="btn btn-link text-decoration-none p-0" :class="post.is_liked ? 'text-primary' : 'text-muted'" @click="toggleLike(post.id)">
                                        <i class="bi" :class="post.is_liked ? 'bi-heart-fill' : 'bi-heart'"></i>
                                        <span class="ms-1 small fw-bold">{{ post.likes_count }}</span>
                                    </button>
                                    <button class="btn btn-link text-decoration-none p-0 text-muted">
                                        <i class="bi bi-chat"></i>
                                        <span class="ms-1 small fw-bold">{{ post.comments_count }}</span>
                                    </button>
                                    <button class="btn btn-link text-decoration-none p-0 text-muted ms-auto">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- COLUMNA DERECHA: Tendencias -->
                <aside class="col-lg-3 d-none d-lg-block">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3 opacity-75">Activos en Tendencia</h6>
                        <div v-for="asset in trendingAssets" :key="asset.id" class="d-flex align-items-center mb-3 p-2 bg-light rounded-3 transition-hover shadow-hover-sm">
                            <div class="bg-primary text-white rounded p-1 me-3 small fw-bold" style="width: 35px; text-align: center;">{{ asset.ticker }}</div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-bold small text-truncate">{{ asset.name }}</div>
                                <div class="text-muted" style="font-size: 11px;">Menciones: {{ asset.posts_count }}</div>
                            </div>
                            <div class="text-success small fw-bold">+{{ (Math.random() * 5).toFixed(1) }}%</div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-primary-subtle border-primary-subtle">
                        <div class="display-6 mt-2 mb-3">🚀</div>
                        <h6 class="fw-bold mb-1 text-primary">Pipkonx Alpha</h6>
                        <p class="small text-muted mb-3 font-italic">Únete a la comunidad de inversores más activa y comparte tus análisis.</p>
                        <button class="btn btn-primary btn-sm rounded-pill px-4">Saber más</button>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SocialIndex',
    props: {
        posts: Object,
        featuredPost: Object,
        filters: Object,
        usersToFollow: Array, // Note: The prop name in Blade is users-to-follow
        trendingAssets: Array,
        famousPortfolios: Array,
        user: Object
    },
    data() {
        return {
            activeFilter: this.filters.tab || 'recent',
            newPost: {
                content: '',
                image: null
            }
        }
    },
    methods: {
        switchFilter(f) {
            window.location.href = `/social?tab=${f}`;
        },
        submitPost() {
            // Simplified for non-AJAX or minimal AJAX
            this.$emit('submit-post', this.newPost);
            this.newPost.content = '';
        },
        toggleLike(id) {
            window.location.href = `/social/like?likeable_id=${id}&likeable_type=post`;
        },
        deletePost(id) {
            if (confirm('¿Estás seguro de eliminar esta publicación?')) {
                window.location.href = `/social/post/${id}/delete`;
            }
        }
    }
}
</script>

<style scoped>
.rounded-4 { border-radius: 1rem !important; }
.bg-primary-subtle { background-color: #e7f1ff !important; }
.transition-hover {
    transition: all 0.2s ease;
}
.transition-hover:hover {
    background-color: #dee2e6 !important;
}
</style>
