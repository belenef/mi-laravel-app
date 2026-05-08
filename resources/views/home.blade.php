@extends('layouts.app')

@section('content')
    @php
    $emotionData = [
        'Felicidad' => ['color' => '#ffc107', 'emoji' => '😊'],
        'Calma' => ['color' => '#0dcaf0', 'emoji' => '😌'],
        'Tristeza' => ['color' => '#6c757d', 'emoji' => '😢'],
        'Energía' => ['color' => '#dc3545', 'emoji' => '⚡']
    ];
    @endphp
    <h3 class="mb-4 fw-bold text-purple">Explorar playlists</h3>
    <div class="row">
        <div class="col-lg-8">
            <div id="playlistsContainer">
                @foreach($playlists as $p)
                <a href="{{ route('playlists.show', $p) }}" class="text-decoration-none">
                <div class="card mb-5 shadow-sm border-0 overflow-hidden" data-playlist-id="{{ $p->id }}" data-playlist-title="{{ strtolower($p->title) }}" data-playlist-mood="{{ strtolower($p->mood) }}" style="height: 200px;">
                    <div class="row g-0 h-100">
                        <div class="col-md-4 h-100 playlist-cover-container">
                            @if($p->cover)
                                @if(str_starts_with($p->cover, 'auto:'))
                                    @php
                                    $mood = explode(':', $p->cover)[1];
                                    $emotion = $emotionData[$mood] ?? ['color' => '#6f42c1', 'emoji' => '🎵'];
                                    @endphp
                                    <div class="playlist-cover h-100 d-flex align-items-center justify-content-center" style="background: #995cfb;">
                                        <div class="text-white fs-1">{{ $emotion['emoji'] }}</div>
                                    </div>
                                @elseif(str_starts_with($p->cover, 'http'))
                                    <img src="{{ $p->cover }}" class="img-fluid rounded-start h-100 w-100" style="object-fit: cover; object-position: center;" alt="cover">
                                @else
                                    <img src="{{ asset('storage/' . $p->cover) }}?t={{ $p->updated_at->timestamp }}"
                                        class="img-fluid rounded-start h-100 w-100"
                                        style="object-fit: cover; object-position: center;"
                                        alt="cover"
                                        onerror="this.onerror=null; this.parentNode.innerHTML = `<div class='playlist-cover h-100 d-flex align-items-center justify-content-center'><div class='text-white fs-1'><i class='bi bi-music-note'></i></div></div>`;">
                                @endif
                                                            @else
                                                                <div class="playlist-cover h-100 d-flex align-items-center justify-content-center">
                                                                    <div class="text-white fs-1"><i class="bi bi-music-note"></i></div>
                                                                </div>
                                                            @endif
                        </div>
                        <div class="col-md-8 h-100 d-flex flex-column">
                            <div class="card-body d-flex flex-column justify-content-center h-100">
                                <h5 class="card-title fw-bold text-purple">{{ $p->title }}</h5>
                                <p class="card-text text-muted">{{ $p->description ?? 'Sin descripción' }}</p>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 24px; height: 24px; font-size: 12px;">{{ substr($p->user->name, 0, 1) }}
                                    </div>
                                    <span class="small">Por <strong>{{ $p->user->name }}</strong></span>
                                </div>
                                <span class="badge bg-light text-purple">{{ $p->mood }}</span>
                                <div class="mt-3 d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-purple like-btn" data-playlist-id="{{ $p->id }}" title="Me gusta">
                                        <i class="bi bi-heart"></i> <span class="like-count">0</span>
                                    </button>
                                    <button class="btn btn-sm btn-outline-purple" data-bs-toggle="modal" data-bs-target="#commentsModal" onclick="loadComments({{ $p->id }})" title="Comentarios">
                                        <i class="bi bi-chat-dots"></i> <span class="comments-count">0</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 shadow-sm border-0 mb-4">
                <h6 class="fw-bold mb-3">Buscar Vibes</h6>
                <div class="input-group">
                    <input id="searchInput" class="form-control border-light" placeholder="¿Qué buscas?">
                    <button id="searchBtn" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <div class="card p-4 shadow-sm border-0 mb-4">
                <h6 class="fw-bold mb-3">Mis Amigos</h6>
                <!-- Amigo 1 -->
                <div class="d-flex align-items-center mb-3">
                    <div class="position-relative me-2 flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">A</div>
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                            style="width: 10px; height: 10px;"></span>
                    </div>
                    <div class="flex-grow-1 small overflow-hidden">
                        <p class="mb-0 fw-bold text-truncate">Ana García</p>
                        <p class="text-muted mb-0 text-truncate" style="font-size: 10px;"><i
                                class="fa-solid fa-compact-disc me-1"></i> Escuchando: Techno Berlin</p>
                    </div>
                    <button class="btn btn-sm btn-link text-purple p-0" title="Colaborar"
                        onclick="openCollaborationModal(this, 'Ana García')"><i
                            class="fa-solid fa-handshake-angle"></i></button>
                </div>
                <!-- Amigo 2 -->
                <div class="d-flex align-items-center mb-3">
                    <div class="position-relative me-2 flex-shrink-0">
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">L</div>
                        <span class="position-absolute bottom-0 end-0 bg-secondary border border-white rounded-circle"
                            style="width: 10px; height: 10px;"></span>
                    </div>
                    <div class="flex-grow-1 small overflow-hidden">
                        <p class="mb-0 fw-bold text-truncate">Luis Rock</p>
                        <p class="text-muted mb-0 text-truncate" style="font-size: 10px;">Offline hace 2h</p>
                    </div>
                </div>
                <hr class="my-2 opacity-10">
                <button class="btn btn-sm btn-outline-purple w-100 rounded-pill mt-2" onclick="openAllFriendsModal()">Ver todos los amigos</button>
            </div>
        </div>
    </div>

    <!-- Modal de Colaboración -->
    <div class="modal fade" id="collaborationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title text-purple fw-bold">Solicitud de Colaboración</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fa-solid fa-handshake-angle text-purple" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p class="lead fw-bold mb-2">¿Colaborar con <span id="collaboratorName"></span>?</p>
                    <p class="text-muted small">Enviar una solicitud de colaboración para crear una playlist conjunta</p>
                </div>
                <div class="modal-footer border-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-purple" onclick="sendCollaborationRequest()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Todos los Amigos -->
    <div class="modal fade" id="allFriendsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title text-purple fw-bold">Mis Amigos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="friendsList" class="space-y-3">
                        <!-- Amigos dinámicos aquí -->
                        <div class="d-flex align-items-center p-2 rounded hover-effect" style="cursor: pointer;">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                style="width: 40px; height: 40px;">A</div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 fw-bold text-truncate">Ana García</p>
                                <p class="text-muted mb-0 text-truncate small"><i class="fa-solid fa-circle text-success me-1" style="font-size: 0.5rem;"></i> En línea</p>
                            </div>
                            <button class="btn btn-sm btn-outline-purple rounded-pill">Perfil</button>
                        </div>
                        <div class="d-flex align-items-center p-2 rounded hover-effect" style="cursor: pointer;">
                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                style="width: 40px; height: 40px;">L</div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 fw-bold text-truncate">Luis Rock</p>
                                <p class="text-muted mb-0 text-truncate small">Offline hace 2h</p>
                            </div>
                            <button class="btn btn-sm btn-outline-purple rounded-pill">Perfil</button>
                        </div>
                        <div class="d-flex align-items-center p-2 rounded hover-effect" style="cursor: pointer;">
                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                style="width: 40px; height: 40px;">A</div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 fw-bold text-truncate">Anónimo</p>
                                <p class="text-muted mb-0 text-truncate small">Offline hace 3 días</p>
                            </div>
                            <button class="btn btn-sm btn-outline-purple rounded-pill">Perfil</button>
                        </div>
                        <div class="d-flex align-items-center p-2 rounded hover-effect" style="cursor: pointer;">
                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                style="width: 40px; height: 40px;">N</div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 fw-bold text-truncate">Noseeee</p>
                                <p class="text-muted mb-0 text-truncate small"><i class="fa-solid fa-circle text-success me-1" style="font-size: 0.5rem;"></i> En línea</p>
                            </div>
                            <button class="btn btn-sm btn-outline-purple rounded-pill">Perfil</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Comentarios -->
    <div class="modal fade" id="commentsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content border-0">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title text-purple fw-bold">Comentarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario de comentario -->
                    <form id="commentForm" class="mb-4">
                        <div class="d-flex gap-2">
                            <input type="text" id="commentInput" class="form-control form-control-sm" 
                                placeholder="Escribe un comentario..." maxlength="1000">
                            <button type="submit" class="btn btn-sm btn-purple">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Lista de comentarios -->
                    <div id="commentsList" class="space-y-3">
                        <div class="text-center text-muted py-4">
                            <p>Cargando comentarios...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Variable global para el ID del usuario autenticado
        const authUserId = {{ auth()->id() ?? 'null' }};
        
        // ===== FUNCIONALIDAD DE BÚSQUEDA =====
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const playlistsContainer = document.getElementById('playlistsContainer');
            const playlists = playlistsContainer.querySelectorAll('.card');

            function filterPlaylists(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                
                playlists.forEach(playlist => {
                    const title = playlist.getAttribute('data-playlist-title');
                    const mood = playlist.getAttribute('data-playlist-mood');
                    
                    if (title.includes(term) || mood.includes(term) || term === '') {
                        playlist.style.display = '';
                    } else {
                        playlist.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('keyup', function() {
                filterPlaylists(this.value);
            });

            searchBtn.addEventListener('click', function() {
                filterPlaylists(searchInput.value);
            });

            // ===== CARGAR IMÁGENES DE LOCALSTORAGE =====
            setTimeout(function() {
                playlists.forEach(playlist => {
                    const playlistId = playlist.getAttribute('data-playlist-id');
                    const savedCover = localStorage.getItem(`playlist_cover_${playlistId}`);
                    
                    if (savedCover) {
                        console.log('Cargando portada para playlist', playlistId);
                        const coverContainer = playlist.querySelector('.playlist-cover-container');
                        if (coverContainer) {
                            console.log('Contenedor encontrado, reemplazando HTML');
                            coverContainer.innerHTML = `<img src="${savedCover}" class="img-fluid h-100 w-100" style="object-fit: cover; object-position: center;" alt="cover">`;
                        } else {
                            console.log('Contenedor no encontrado para playlist', playlistId);
                        }
                    }
                });
            }, 100);
        });

        // ===== FUNCIONALIDAD DE COLABORACIÓN =====
        function openCollaborationModal(btn, userName) {
            const collaboratorName = document.getElementById('collaboratorName');
            collaboratorName.textContent = userName;
            
            // Guardar el ID del usuario para usar después
            window.currentCollaborator = {
                name: userName,
                btn: btn
            };
            
            const modal = new bootstrap.Modal(document.getElementById('collaborationModal'));
            modal.show();
        }

        function sendCollaborationRequest() {
            if (window.currentCollaborator) {
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('collaborationModal'));
                modal.hide();
                
                // Mostrar notificación de éxito
                showSuccessNotification(`Solicitud de colaboración enviada a ${window.currentCollaborator.name}`);
            }
        }

        // ===== FUNCIONALIDAD VER TODOS LOS AMIGOS =====
        function openAllFriendsModal() {
            const modal = new bootstrap.Modal(document.getElementById('allFriendsModal'));
            modal.show();
        }

        // ===== NOTIFICACIÓN DE ÉXITO =====
        function showSuccessNotification(message) {
            const notificationHTML = `
                <div class="alert alert-success alert-dismissible fade show position-fixed" role="alert" 
                    style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <i class="bi bi-check-circle me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            const container = document.createElement('div');
            container.innerHTML = notificationHTML;
            document.body.appendChild(container.firstElementChild);
            
            // Auto-eliminar después de 3 segundos
            setTimeout(() => {
                const alert = document.querySelector('.alert.alert-success');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 3000);
        }

        // ===== FUNCIONALIDAD DE LIKES =====
        let currentPlaylistId = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Cargar contador de likes al cargar la página
            const likeBtns = document.querySelectorAll('.like-btn');
            likeBtns.forEach(btn => {
                const playlistId = btn.getAttribute('data-playlist-id');
                loadLikeCount(playlistId, btn);
                
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleLike(playlistId, btn);
                });
            });

            // Cargar contador de comentarios al cargar la página
            const playlists = document.querySelectorAll('[data-playlist-id]');
            playlists.forEach(playlist => {
                const playlistId = playlist.getAttribute('data-playlist-id');
                loadCommentsCount(playlistId, playlist);
            });
        });

        function loadLikeCount(playlistId, btn) {
            fetch(`/api/playlists/${playlistId}/likes/count`)
                .then(response => response.json())
                .then(data => {
                    const likeBtn = btn || document.querySelector(`[data-playlist-id="${playlistId}"]`).querySelector('.like-btn');
                    likeBtn.querySelector('.like-count').textContent = data.count;
                    
                    if (data.userLiked) {
                        likeBtn.classList.add('liked');
                        likeBtn.querySelector('i').classList.remove('bi-heart');
                        likeBtn.querySelector('i').classList.add('bi-heart-fill');
                    }
                })
                .catch(error => console.error('Error cargando likes:', error));
        }

        function toggleLike(playlistId, btn) {
            const isLiked = btn.classList.contains('liked');

            if (isLiked) {
                // Quitar like
                fetch(`/api/playlists/${playlistId}/likes`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => { throw data; });
                    }
                    return response.json();
                })
                .then(data => {
                    btn.classList.remove('liked');
                    btn.querySelector('i').classList.remove('bi-heart-fill');
                    btn.querySelector('i').classList.add('bi-heart');
                    btn.querySelector('.like-count').textContent = data.count;
                    showSuccessNotification('Like quitado');
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    if (error.message === 'Unauthenticated.') {
                        showErrorNotification('Debes iniciar sesión para dar likes');
                    } else {
                        showErrorNotification('No se pudo quitar el like');
                    }
                });
            } else {
                // Agregar like
                fetch(`/api/playlists/${playlistId}/likes`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => { throw data; });
                    }
                    return response.json();
                })
                .then(data => {
                    btn.classList.add('liked');
                    btn.querySelector('i').classList.remove('bi-heart');
                    btn.querySelector('i').classList.add('bi-heart-fill');
                    btn.querySelector('.like-count').textContent = data.count;
                    showSuccessNotification('¡Te gusta esta playlist!');
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    if (error.message === 'Ya has dado like a esta playlist') {
                        showErrorNotification('Ya has dado like a esta playlist');
                    } else if (error.message === 'Unauthenticated.') {
                        showErrorNotification('Debes iniciar sesión para dar likes');
                    } else {
                        showErrorNotification('No se pudo agregar el like');
                    }
                });
            }
        }

        // ===== FUNCIONALIDAD DE COMENTARIOS =====
        function loadComments(playlistId) {
            currentPlaylistId = playlistId;
            const commentsList = document.getElementById('commentsList');
            commentsList.innerHTML = '<div class="text-center text-muted py-4"><p>Cargando comentarios...</p></div>';

            fetch(`/api/playlists/${playlistId}/comments`)
                .then(response => response.json())
                .then(comments => {
                    if (comments.length === 0) {
                        commentsList.innerHTML = '<div class="text-center text-muted py-4"><p>No hay comentarios aún</p></div>';
                    } else {
                        commentsList.innerHTML = comments.map(comment => `
                            <div class="d-flex gap-2 pb-3 border-bottom">
                                <div class="bg-light text-dark rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 32px; height: 32px; font-size: 12px;">
                                    ${comment.user.name.charAt(0).toUpperCase()}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="mb-1">
                                        <strong class="text-purple">${escapeHtml(comment.user.name)}</strong>
                                    </p>
                                    <p class="mb-1 text-wrap" style="word-break: break-word;">${escapeHtml(comment.content)}</p>
                                    <small class="text-muted">${formatDate(comment.created_at)}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    ${comment.user_id === authUserId ? 
                                        `<button class="btn btn-sm btn-link text-danger p-0" onclick="deleteComment(${comment.id})" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>` 
                                        : ''}
                                </div>
                            </div>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error cargando comentarios:', error);
                    commentsList.innerHTML = '<div class="text-center text-danger py-4"><p>Error al cargar comentarios</p></div>';
                });

            // Configurar formulario de comentario
            const commentForm = document.getElementById('commentForm');
            commentForm.onsubmit = function(e) {
                e.preventDefault();
                addComment();
            };
        }

        function loadCommentsCount(playlistId, playlistElement) {
            fetch(`/api/playlists/${playlistId}/comments`)
                .then(response => response.json())
                .then(comments => {
                    const countElement = playlistElement.querySelector('.comments-count');
                    if (countElement) {
                        countElement.textContent = comments.length;
                    }
                })
                .catch(error => console.error('Error cargando comentarios:', error));
        }

        function addComment() {
            const commentInput = document.getElementById('commentInput');
            const content = commentInput.value.trim();

            if (!content) {
                showErrorNotification('El comentario no puede estar vacío');
                return;
            }

            fetch(`/api/playlists/${currentPlaylistId}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw data; });
                }
                return response.json();
            })
            .then(data => {
                commentInput.value = '';
                loadComments(currentPlaylistId);
                loadCommentsCount(currentPlaylistId, document.querySelector(`[data-playlist-id="${currentPlaylistId}"]`));
                showSuccessNotification('Comentario agregado');
            })
            .catch(error => {
                console.error('Error completo:', error);
                if (error.message === 'Unauthenticated.') {
                    showErrorNotification('Debes iniciar sesión para comentar');
                } else {
                    showErrorNotification('No se pudo agregar el comentario');
                }
            });
        }

        function deleteComment(commentId) {
            if (!confirm('¿Estás seguro de que deseas eliminar este comentario?')) {
                return;
            }

            fetch(`/api/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw data; });
                }
                return response.json();
            })
            .then(data => {
                loadComments(currentPlaylistId);
                showSuccessNotification('Comentario eliminado');
            })
            .catch(error => {
                console.error('Error completo:', error);
                showErrorNotification('No se pudo eliminar el comentario');
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Justo ahora';
            if (diffMins < 60) return `hace ${diffMins} min`;
            if (diffHours < 24) return `hace ${diffHours}h`;
            if (diffDays < 7) return `hace ${diffDays}d`;

            return date.toLocaleDateString('es-ES');
        }

        function showErrorNotification(message) {
            const notificationHTML = `
                <div class="alert alert-danger alert-dismissible fade show position-fixed" role="alert" 
                    style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <i class="bi bi-exclamation-circle me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            const container = document.createElement('div');
            container.innerHTML = notificationHTML;
            document.body.appendChild(container.firstElementChild);
            
            setTimeout(() => {
                const alert = document.querySelector('.alert.alert-danger');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 3000);
        }
    </script>
@endpush