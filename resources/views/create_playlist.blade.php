@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="playlistTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual"
                                type="button" role="tab">Creación Manual</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="auto-tab" data-bs-toggle="tab" data-bs-target="#auto" type="button"
                                role="tab">Generación Automática</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content" id="playlistTabContent">
                    <!-- Manual Tab -->
                    <div class="tab-pane fade show active" id="manual" role="tabpanel">
                        <h4 class="mb-4 mt-2">Crear nueva playlist manualmente</h4>
                        <form method="POST" action="{{ route('playlists.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold">Nombre de la playlist</label>
                                <input type="text" name="title" class="form-control rounded-3"
                                    placeholder="Ej: Energía Mañanera" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Descripción (opcional)</label>
                                <textarea name="description" class="form-control rounded-3" rows="3"
                                    placeholder="¿De qué trata esta lista?"></textarea>
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                    <input class="form-check-input ms-0" type="checkbox" name="is_collaborative"
                                        id="flexSwitchCollaborative" value="1">
                                    <label class="form-check-label fw-bold mb-0" for="flexSwitchCollaborative">
                                        <i class="fa-solid fa-user-group text-purple me-1"></i> Playlist Colaborativa
                                        <br><small class="text-muted fw-normal">Permite que tus amigos sugieran y añadan
                                            canciones.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Portada de la playlist</label>
                                <input type="file" name="cover" id="manualCoverInput" class="form-control" accept="image/*" onchange="previewImageManual(this)">
                                <small class="text-muted">Opcional. Si no subes imagen, se usará una portada automática basada en el estado de ánimo.</small>
                                <div id="manual-cover-preview" style="display: none; margin-top: 10px;">
                                    <img id="manual-preview-img" src="" alt="Preview" class="rounded" style="width: 100px; height: 100px; object-fit: cover; object-position: center;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado de ánimo</label>
                                <select name="mood" id="manualMoodSelect" class="form-select">
                                    <option>Felicidad</option>
                                    <option>Calma</option>
                                    <option>Tristeza</option>
                                    <option>Energía</option>
                                </select>
                            </div>
                            <div id="manualMoodSuggestions" class="mb-4">
                                <div class="card bg-light p-3 rounded-3">
                                    <p class="mb-2 fw-bold">Canciones sugeridas para este mood</p>
                                    <ul id="manualSongSuggestions" class="list-group list-group-flush"></ul>
                                </div>
                            </div>
                            <input type="hidden" name="songs" id="manualInputSongs" value="[]">
                            <button class="btn btn-primary w-100" type="submit">Guardar Playlist</button>
                        </form>
                    </div>

                    <!-- Automatic Tab -->
                    <div class="tab-pane fade" id="auto" role="tabpanel">
                        <h4 class="mb-4 mt-2">Generar playlist por emoción</h4>
                        <p class="text-muted">Elige cómo te sientes y nosotros nos encargamos del resto.</p>

                        <div class="mb-4">
                            <label class="form-label fw-bold">¿Cómo te sientes hoy?</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="emotion" id="emo-happy" value="Felicidad"
                                        autocomplete="off" checked>
                                    <label class="btn btn-outline-warning w-100 py-3" for="emo-happy"><i
                                            class="bi bi-emoji-smile fs-4"></i><br>Felicidad</label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="emotion" id="emo-calm" value="Calma"
                                        autocomplete="off">
                                    <label class="btn btn-outline-info w-100 py-3" for="emo-calm"><i
                                            class="bi bi-water fs-4"></i><br>Calma</label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="emotion" id="emo-sad" value="Tristeza"
                                        autocomplete="off">
                                    <label class="btn btn-outline-secondary w-100 py-3" for="emo-sad"><i
                                            class="bi bi-emoji-frown fs-4"></i><br>Tristeza</label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="emotion" id="emo-energy" value="Energía"
                                        autocomplete="off">
                                    <label class="btn btn-outline-danger w-100 py-3" for="emo-energy"><i
                                            class="bi bi-lightning-charge fs-4"></i><br>Energía</label>
                                </div>
                            </div>
                        </div>

                        <button id="generateBtn" class="btn btn-purple btn-lg w-100 mb-4">
                            <i class="bi bi-magic"></i> Generar Playlist Automática
                        </button>

                        <div id="generationResult" class="d-none mt-4">
                            <h5><i class="bi bi-stars"></i> Playlist Generada: <span id="genTitle"
                                    class="text-purple"></span></h5>
                            <div class="list-group list-group-flush border rounded mb-3">
                                <!-- Songs will appear here -->
                            </div>
                            <form method="POST" action="{{ route('playlists.store') }}" id="autoSaveForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="title" id="autoInputTitle">
                                <input type="hidden" name="description" id="autoInputDesc">
                                <input type="hidden" name="mood" id="autoInputMood">
                                <input type="hidden" name="songs" id="autoInputSongs" value="[]">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success flex-grow-1"><i class="bi bi-check-lg"></i>
                                        Guardar esta
                                        Selección</button>
                                    <button type="button" class="btn btn-outline-secondary" id="regenBtn"><i
                                            class="bi bi-arrow-clockwise"></i> Regenerar</button>
                                </div>
                            </form>
                        </div>
                    </div>
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

        // Variable global para almacenar la imagen comprimida
        let currentCompressedImage = null;

        // ===== PREVIEW DE IMAGEN PARA FORMULARIO MANUAL =====
        function previewImageManual(input) {
            const preview = document.getElementById('manual-cover-preview');
            const previewImg = document.getElementById('manual-preview-img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageData = e.target.result;
                    
                    // Comprimir imagen antes de mostrar
                    compressImage(imageData, function(compressedData) {
                        previewImg.src = compressedData;
                        preview.style.display = 'block';
                        currentCompressedImage = compressedData;
                        
                        // Guardar en sessionStorage INMEDIATAMENTE cuando se selecciona
                        try {
                            sessionStorage.setItem('new_playlist_cover', compressedData);
                            console.log('Imagen guardada en sessionStorage al seleccionar');
                        } catch (err) {
                            console.warn('No se pudo guardar imagen en sessionStorage:', err);
                        }
                    });
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
                currentCompressedImage = null;
                sessionStorage.removeItem('new_playlist_cover');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const generateBtn = document.getElementById('generateBtn');
            const regenBtn = document.getElementById('regenBtn');
            const resultArea = document.getElementById('generationResult');
            const genTitle = document.getElementById('genTitle');
            const songList = resultArea.querySelector('.list-group');

            // Capturar el envío del formulario manual
            const manualForm = document.querySelector('#manual form[method="POST"]');
            if (manualForm) {
                manualForm.addEventListener('submit', function(e) {
                    console.log('Formulario manual siendo enviado');
                    // Guardar la imagen ANTES de que se envíe el formulario
                    // pero sin interferir con la carga del archivo
                    if (currentCompressedImage) {
                        console.log('Imagen guardada en sessionStorage');
                        try {
                            sessionStorage.setItem('new_playlist_cover', currentCompressedImage);
                        } catch (err) {
                            console.warn('No se pudo guardar imagen en sessionStorage:', err);
                            // Continuar de todas formas
                        }
                    }
                    // El formulario se enviará normalmente
                });
            }

            const musicLibrary = {
                "Felicidad": [
                    { title: "Walking on Sunshine", artist: "Katrina & The Waves" },
                    { title: "Happy", artist: "Pharrell Williams" },
                    { title: "Good Vibrations", artist: "The Beach Boys" },
                    { title: "Dancing Queen", artist: "ABBA" },
                    { title: "Can't Stop the Feeling!", artist: "Justin Timberlake" },
                    { title: "Uptown Funk", artist: "Mark Ronson ft. Bruno Mars" },
                    { title: "September", artist: "Earth, Wind & Fire" }
                ],
                "Calma": [
                    { title: "Weightless", artist: "Marconi Union" },
                    { title: "Claire de Lune", artist: "Claude Debussy" },
                    { title: "Riverside", artist: "Agnes Obel" },
                    { title: "Holocene", artist: "Bon Iver" },
                    { title: "River Flows in You", artist: "Yiruma" },
                    { title: "Saturn", artist: "Sleeping At Last" },
                    { title: "Bloom", artist: "The Paper Kites" }
                ],
                "Tristeza": [
                    { title: "Someone Like You", artist: "Adele" },
                    { title: "Stay With Me", artist: "Sam Smith" },
                    { title: "The Night We Met", artist: "Lord Huron" },
                    { title: "Skinny Love", artist: "Bon Iver" },
                    { title: "Fix You", artist: "Coldplay" },
                    { title: "All I Want", artist: "Kodaline" },
                    { title: "Hurt", artist: "Johnny Cash" }
                ],
                "Energía": [
                    { title: "Eye of the Tiger", artist: "Survivor" },
                    { title: "Thunderstruck", artist: "AC/DC" },
                    { title: "Don't Stop Me Now", artist: "Queen" },
                    { title: "Can't Hold Us", artist: "Macklemore & Ryan Lewis" },
                    { title: "Titanium", artist: "David Guetta ft. Sia" },
                    { title: "Stronger", artist: "Kanye West" },
                    { title: "Levels", artist: "Avicii" }
                ]
            };

            function pickRandomSongs(songs, count) {
                const copy = [...songs];
                for (let i = copy.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [copy[i], copy[j]] = [copy[j], copy[i]];
                }
                return copy.slice(0, count);
            }

            function renderSongList(songs, container) {
                container.innerHTML = '';
                songs.forEach(song => {
                    const item = document.createElement('div');
                    item.className = 'list-group-item d-flex align-items-center';
                    item.innerHTML = `
                                <div class="me-3 text-purple"><i class="bi bi-music-note-beamed"></i></div>
                                <div>
                                    <div class="fw-bold">${song.title}</div>
                                    <div class="small text-muted">${song.artist}</div>
                                </div>
                            `;
                    container.appendChild(item);
                });
            }

            const manualSongsInput = document.getElementById('manualInputSongs');
            const autoSongsInput = document.getElementById('autoInputSongs');

            function renderManualSuggestions() {
                const mood = document.getElementById('manualMoodSelect').value;
                const songs = musicLibrary[mood] || [];
                const selected = pickRandomSongs(songs, 3);
                const suggestions = document.getElementById('manualSongSuggestions');
                suggestions.innerHTML = '';
                manualSongsInput.value = JSON.stringify(selected);

                selected.forEach(song => {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item border-0 px-0 py-2';
                    listItem.innerHTML = `<strong>${song.title}</strong><br><span class="small text-muted">${song.artist}</span>`;
                    suggestions.appendChild(listItem);
                });
            }

            function generate() {
                const emotion = document.querySelector('input[name="emotion"]:checked').value;
                const songs = musicLibrary[emotion] || [];
                const selectedSongs = pickRandomSongs(songs, 3);

                genTitle.textContent = `Vibes de ${emotion}`;
                renderSongList(selectedSongs, songList);

                document.getElementById('autoInputTitle').value = `Vibes de ${emotion}`;
                document.getElementById('autoInputDesc').value = `Playlist generada automáticamente para un estado de ánimo de ${emotion}.`;
                document.getElementById('autoInputMood').value = emotion;
                autoSongsInput.value = JSON.stringify(selectedSongs);

                resultArea.classList.remove('d-none');
                resultArea.scrollIntoView({ behavior: 'smooth' });
            }

            const manualMoodSelect = document.getElementById('manualMoodSelect');
            manualMoodSelect.addEventListener('change', renderManualSuggestions);
            renderManualSuggestions();

            generateBtn.addEventListener('click', generate);
            regenBtn.addEventListener('click', generate);
        });
    </script>
@endpush