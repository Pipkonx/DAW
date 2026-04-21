<template>
    <div class="py-4 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <!-- Banner & Avatar Header -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                    <img v-if="profileUser.banner_path" :src="'/storage/' + profileUser.banner_path" class="w-100 h-100 object-fit-cover">
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="d-flex align-items-end mb-4" style="margin-top: -60px;">
                        <div class="position-relative">
                            <img :src="profileUser.avatar || ('https://ui-avatars.com/api/?name=' + profileUser.name)" 
                                 class="rounded-circle border border-5 border-white shadow-sm" 
                                 width="120" height="120">
                        </div>
                        <div class="ms-4 mb-2 flex-grow-1">
                            <h2 class="fw-black text-dark mb-0 d-flex align-items-center">
                                {{ profileUser.name }}
                                <i v-if="profileUser.tier !== 'none'" class="bi bi-patch-check-fill text-primary ms-2 small"></i>
                            </h2>
                            <p class="text-muted fw-bold small mb-0">@{{ profileUser.username || 'user_' + profileUser.id }}</p>
                        </div>
                        <div class="mb-2">
                            <button v-if="isOwnProfile" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" @click="goToEdit">
                                <i class="bi bi-pencil me-1"></i> Editar perfil
                            </button>
                            <div v-else class="d-flex gap-2">
                                <button class="btn rounded-pill px-4 fw-bold" 
                                        :class="isFollowing ? 'btn-light' : 'btn-primary'" 
                                        @click="toggleFollow">
                                    {{ isFollowing ? 'Siguiendo' : 'Seguir' }}
                                </button>
                                <button class="btn btn-light rounded-circle" @click="toggleBlock">
                                    <i class="bi" :class="isBlocked ? 'bi-slash-circle-fill text-danger' : 'bi-slash-circle'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-8">
                            <p class="lead small text-dark mb-4" style="white-space: pre-wrap;">{{ profileUser.bio || 'Sin biografía disponible.' }}</p>
                            
                            <div class="d-flex flex-wrap gap-4 text-muted small mb-4">
                                <span><i class="bi bi-calendar3 me-2"></i>Se unió en {{ joinedAt }}</span>
                                <span class="fw-bold text-dark"><i class="bi bi-people me-2"></i>{{ profileUser.followers_count }} <span class="text-muted fw-normal">Seguidores</span></span>
                                <span class="fw-bold text-dark">{{ profileUser.following_count }} <span class="text-muted fw-normal">Siguiendo</span></span>
                            </div>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs nav-fill border-0 mb-4 bg-white p-1 rounded-pill shadow-sm">
                                <li class="nav-item" v-for="tab in tabs" :key="tab.id">
                                    <button class="nav-link rounded-pill border-0 fw-bold px-4 transition-all"
                                            :class="{ active: activeTab === tab.id }"
                                            @click="activeTab = tab.id">
                                        {{ tab.label }}
                                    </button>
                                </li>
                            </ul>

                            <!-- Posts Feed -->
                            <div class="space-y-4">
                                <div v-if="displayPosts.length === 0" class="text-center py-5 bg-white rounded-4 border border-dashed text-muted">
                                    <p class="mb-0 fw-bold">No hay publicaciones para mostrar aquí.</p>
                                </div>
                                
                                <div v-for="post in displayPosts" :key="post.id" class="card border-0 shadow-sm rounded-4 mb-4">
                                    <div class="card-body p-4">
                                        <p class="mb-3">{{ post.content }}</p>
                                        <div v-if="post.image_path" class="mb-3 rounded-4 overflow-hidden">
                                            <img :src="'/storage/' + post.image_path" class="img-fluid w-100">
                                        </div>
                                        <div class="d-flex gap-4 text-muted small border-top pt-3">
                                            <span><i class="bi bi-heart me-1"></i>{{ post.likes_count }}</span>
                                            <span><i class="bi bi-chat me-1"></i>{{ post.comments_count }}</span>
                                            <span v-if="post.wall_is_repost" class="badge bg-light text-primary rounded-pill">Difundido</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Widgets -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white">
                                <h5 class="fw-bold mb-3">Resumen de Actividad</h5>
                                <div class="small opacity-75 mb-4">Estadísticas de participación en la comunidad.</div>
                                <div class="row text-center g-2">
                                    <div class="col-6">
                                        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-white">
                                            <div class="h4 fw-bold mb-0">{{ posts.data ? posts.data.length : posts.length }}</div>
                                            <div class="small opacity-75">Análisis</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-white">
                                            <div class="h4 fw-bold mb-0">{{ bookmarks.length }}</div>
                                            <div class="small opacity-75">Favoritos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="profileUser.famous_portfolios && profileUser.famous_portfolios.length > 0" class="card border-0 shadow-sm rounded-4 p-4">
                                <h6 class="fw-bold mb-3 uppercase small text-muted opacity-75">Carteras de Referencia</h6>
                                <!-- Mock gurus if needed -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ProfileShow',
    props: {
        profileUser: Object,
        posts: [Object, Array],
        bookmarks: Array,
        isOwnProfile: Boolean,
        isFollowing: Boolean,
        isBlocked: Boolean,
        joinedAt: String
    },
    data() {
        return {
            activeTab: 'all',
            tabs: [
                { id: 'all', label: 'Todo' },
                { id: 'posts', label: 'Análisis' },
                { id: 'reposts', label: 'Difusiones' },
                { id: 'bookmarks', label: 'Guardados' }
            ]
        }
    },
    computed: {
        displayPosts() {
            const postsArray = this.posts.data || this.posts || [];
            if (this.activeTab === 'all') return postsArray;
            if (this.activeTab === 'posts') return postsArray.filter(p => !p.wall_is_repost);
            if (this.activeTab === 'reposts') return postsArray.filter(p => p.wall_is_repost);
            if (this.activeTab === 'bookmarks') return this.bookmarks || [];
            return postsArray;
        }
    },
    methods: {
        goToEdit() {
            window.location.href = '/profile';
        },
        toggleFollow() {
            window.location.href = `/profile/${this.profileUser.id}/follow`;
        },
        toggleBlock() {
            if (confirm(this.isBlocked ? '¿Desbloquear a este usuario?' : '¿Estás seguro de bloquear a este usuario?')) {
                window.location.href = `/profile/${this.profileUser.id}/block`;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.nav-link.active {
    background-color: #0d6efd !important;
    color: white !important;
}
.transition-all { transition: all 0.3s ease; }
</style>
