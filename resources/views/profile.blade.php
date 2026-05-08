@extends('layouts.app')

@section('content')
    @php
        $user = Auth::user();
        $myPlaylists = $user->playlists;
    @endphp

    <div class="row">
        <!-- Columna Izquierda: Perfil -->
        <div class="col-md-4">
            <div class="card p-4 mb-3 text-center" id="profile-view">
                <div class="avatar mx-auto mb-3" id="user-avatar-display" 
                    @if($user->avatar)
                        style="background-image: url('{{ asset('storage/' . $user->avatar) }}'); background-size: cover;"
                    @endif
                >
                    @if(!$user->avatar)
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <h4 id="display-name">{{ $user->name }}</h4>
                <p class="text-muted small" id="display-email">{{ $user->email }}</p>
                <hr>
                <p class="mb-3" id="display-bio"><em>"{{ $user->bio }}"</em></p>

                <!-- Stats de Seguidores -->
                <div class="d-flex justify-content-center gap-4 mb-4">
                    <div class="text-center">
                        <h5 class="fw-bold mb-0" id="follower-count">124</h5>
                        <small class="text-muted">Seguidores</small>
                    </div>
                    <div class="text-center">
                        <h5 class="fw-bold mb-0" id="following-count">89</h5>
                        <small class="text-muted">Siguiendo</small>
                    </div>
                </div>

                <button class="btn btn-outline-purple w-100" onclick="toggleEdit()">Editar Perfil</button>
            </div>

            <!-- Formulario de Edición (Oculto inicialmente) -->
            <div class="card p-4 mb-3 d-none" id="profile-edit">
                <h5 class="mb-3">Editar Perfil</h5>
                <form id="edit-form">
                    <div class="mb-3 text-center">
                        <div class="avatar mx-auto mb-2" id="edit-avatar-preview">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <label class="btn btn-sm btn-light border">
                            Cambiar Imagen
                            <input type="file" id="avatar-input" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                        </label>
                        
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" id="input-name" class="form-control" value="{{ $user->name }}">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" id="input-email" class="form-control" value="{{ $user->email }}">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Descripción corta</label>
                        <textarea id="input-bio" class="form-control" rows="3">{{ $user->bio }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-grow-1" onclick="saveProfile()">Guardar</button>
                        <button type="button" class="btn btn-light border" onclick="toggleEdit()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Columna Derecha: Playlists -->
        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="mb-3"><i class="bi bi-collection-play"></i> Tus playlists</h5>
                <div class="list-group list-group-flush">
                    @forelse($myPlaylists as $pl)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <span class="fw-bold">{{ $pl->title }}</span>
                            <span class="badge bg-purple rounded-pill shadow-sm">0 likes</span>
                        </div>
                    @empty
                        <p class="text-muted small">No has creado ninguna playlist todavía.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ===== FUNCIÓN DE COMPRESIÓN DE IMAGEN =====
        function compressImage(imageData, callback) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const maxWidth = 200;
                const maxHeight = 200;
                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > maxWidth) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                // Comprimir a JPEG con calidad reducida
                const compressedData = canvas.toDataURL('image/jpeg', 0.7);
                callback(compressedData);
            };
            img.src = imageData;
        }

        // Variable global para almacenar avatar comprimido
        let currentCompressedAvatar = null;
        const userId = {{ $user->id }};

        function toggleEdit() {
            document.getElementById('profile-view').classList.toggle('d-none');
            document.getElementById('profile-edit').classList.toggle('d-none');
            
            // Si estamos abriendo el formulario de edición
            if (!document.getElementById('profile-edit').classList.contains('d-none')) {
                console.log('Abriendo formulario de edición, cargando avatar guardado');
                const savedAvatar = localStorage.getItem(`user_avatar_${userId}`);
                if (savedAvatar) {
                    const editPreview = document.getElementById('edit-avatar-preview');
                    editPreview.style.backgroundImage = `url('${savedAvatar}')`;
                    editPreview.style.backgroundSize = 'cover';
                    editPreview.style.backgroundPosition = 'center';
                    editPreview.textContent = '';
                    currentCompressedAvatar = savedAvatar;
                }
            }
        }

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imageData = e.target.result;
                    
                    // Comprimir imagen
                    compressImage(imageData, function(compressedData) {
                        const preview = document.getElementById('edit-avatar-preview');
                        preview.style.backgroundImage = `url(${compressedData})`;
                        preview.style.backgroundSize = 'cover';
                        preview.textContent = '';
                        currentCompressedAvatar = compressedData;
                        
                        // Guardar en sessionStorage
                        try {
                            sessionStorage.setItem('user_avatar_temp', compressedData);
                            console.log('Avatar comprimido y guardado en sessionStorage');
                        } catch (err) {
                            console.warn('No se pudo guardar avatar en sessionStorage:', err);
                        }
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function saveProfile() {
            const name = document.getElementById('input-name').value;
            const email = document.getElementById('input-email').value;
            const bio = document.getElementById('input-bio').value;
            const avatarInput = document.getElementById('avatar-input');
            const token = document.querySelector('meta[name="csrf-token"]').content;

            // Crear FormData
            const formData = new FormData();
            formData.append('_token', token);
            formData.append('name', name);
            formData.append('email', email);
            formData.append('bio', bio);
            
            // Agregar archivo si existe
            if (avatarInput.files.length > 0) {
                formData.append('avatar', avatarInput.files[0]);
                console.log('✓ Archivo agregado:', avatarInput.files[0].name);
            } else {
                console.log('⚠ No hay archivo seleccionado');
            }

            console.log('Enviando FormData con:', {
                tiene_archivo: avatarInput.files.length > 0,
                nombre_archivo: avatarInput.files[0]?.name,
                tamaño: avatarInput.files[0]?.size
            });

            // Hacer la petición
            fetch('/profile/update', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Respuesta del servidor:', data);
                
                if (data.success) {
                    // Actualizar la vista
                    document.getElementById('display-name').textContent = name;
                    document.getElementById('display-email').textContent = email;
                    document.getElementById('display-bio').innerHTML = `<em>"${bio}"</em>`;
                    
                    // Guardar avatar comprimido en localStorage si existe
                    if (currentCompressedAvatar) {
                        try {
                            localStorage.setItem(`user_avatar_${userId}`, currentCompressedAvatar);
                            console.log('Avatar guardado en localStorage');
                        } catch (err) {
                            console.warn('No se pudo guardar avatar en localStorage:', err);
                        }
                    }
                    
                    // Actualizar avatar en la vista
                    const avatarDisplay = document.getElementById('user-avatar-display');
                    if (currentCompressedAvatar) {
                        console.log('Mostrando avatar comprimido');
                        avatarDisplay.style.backgroundImage = `url('${currentCompressedAvatar}')`;
                        avatarDisplay.style.backgroundSize = 'cover';
                        avatarDisplay.style.backgroundPosition = 'center';
                        avatarDisplay.textContent = '';
                    } else if (data.avatar_url) {
                        const imageUrl = data.avatar_url + '?t=' + Date.now();
                        console.log('Mostrando avatar desde:', imageUrl);
                        avatarDisplay.style.backgroundImage = `url('${imageUrl}')`;
                        avatarDisplay.style.backgroundSize = 'cover';
                        avatarDisplay.style.backgroundPosition = 'center';
                        avatarDisplay.textContent = '';
                    } else {
                        avatarDisplay.textContent = name.charAt(0).toUpperCase();
                        avatarDisplay.style.backgroundImage = '';
                    }
                    
                    // Limpiar
                    avatarInput.value = '';
                    currentCompressedAvatar = null;
                    sessionStorage.removeItem('user_avatar_temp');
                    toggleEdit();
                    alert('✓ Perfil actualizado con éxito!');
                } else {
                    alert('✗ ' + (data.message || 'Error al actualizar'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión: ' + error.message);
            });
        }

        // Cargar avatar desde localStorage cuando carga la página
        document.addEventListener('DOMContentLoaded', function() {
            const savedAvatar = localStorage.getItem(`user_avatar_${userId}`);
            if (savedAvatar) {
                console.log('Cargando avatar desde localStorage');
                const avatarDisplay = document.getElementById('user-avatar-display');
                avatarDisplay.style.backgroundImage = `url('${savedAvatar}')`;
                avatarDisplay.style.backgroundSize = 'cover';
                avatarDisplay.style.backgroundPosition = 'center';
                avatarDisplay.textContent = '';
            }
        });
    </script>
@endpush