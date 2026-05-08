@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Editar Playlist</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('playlists.update', $playlist) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $playlist->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $playlist->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="mood" class="form-label">Estado de ánimo</label>
                            <select class="form-select" id="mood" name="mood" required>
                                <option value="happy" {{ old('mood', $playlist->mood) === 'happy' ? 'selected' : '' }}>Feliz 😊</option>
                                <option value="sad" {{ old('mood', $playlist->mood) === 'sad' ? 'selected' : '' }}>Triste 😢</option>
                                <option value="energetic" {{ old('mood', $playlist->mood) === 'energetic' ? 'selected' : '' }}>Enérgico ⚡</option>
                                <option value="relaxed" {{ old('mood', $playlist->mood) === 'relaxed' ? 'selected' : '' }}>Relajado 🎵</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="cover" class="form-label">Portada (opcional)</label>
                            <input type="file" class="form-control" id="cover" name="cover" accept="image/*" onchange="previewImage(this)">
                            <div class="form-text">Deja vacío para mantener la portada actual</div>
                            <div id="current-cover" class="mt-2">
                                @if($playlist->cover && file_exists(storage_path('app/public/' . $playlist->cover)))
                                    <img src="{{ asset('storage/' . $playlist->cover) }}?t={{ $playlist->updated_at->timestamp }}" alt="Current cover" class="rounded" style="width: 100px; height: 100px; object-fit: cover; object-position: center;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                        <span class="text-white fs-2">{{ $playlist->mood === 'Felicidad' ? '😊' : ($playlist->mood === 'Tristeza' ? '😢' : ($playlist->mood === 'Energía' ? '⚡' : '🎵')) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div id="image-preview" class="mt-2" style="display: none;">
                                <img id="preview-img" src="" alt="Preview" class="rounded" style="width: 100px; height: 100px; object-fit: cover; object-position: center;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_collaborative" name="is_collaborative" value="1" {{ old('is_collaborative', $playlist->is_collaborative) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_collaborative">
                                    Playlist colaborativa
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="songs" id="songs-input" value="{{ old('songs', $playlist->songs) }}">

                        <div class="mb-3">
                            <label class="form-label">Canciones</label>
                            <div id="songs-list">
                                @php $songs = json_decode($playlist->songs, true); @endphp
                                @if($songs)
                                    @foreach($songs as $index => $song)
                                        <div class="song-item d-flex align-items-center justify-content-between p-2 border mb-2">
                                            <div>
                                                <strong>{{ $song['title'] ?? 'Sin título' }}</strong> - {{ $song['artist'] ?? 'Artista desconocido' }}
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-song" data-song-id="{{ $index }}">Eliminar</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Actualizar Playlist</button>
                            <a href="{{ route('playlists.show', $playlist) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const playlistId = {{ $playlist->id }};

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

function previewImage(input) {
    console.log('previewImage llamado');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const currentCover = document.getElementById('current-cover');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imageData = e.target.result;
            
            // Comprimir imagen antes de guardar
            compressImage(imageData, function(compressedData) {
                previewImg.src = compressedData;
                preview.style.display = 'block';
                currentCover.style.display = 'none';
                
                // Guardar la imagen comprimida en localStorage
                try {
                    console.log('Guardando imagen comprimida en localStorage para playlist', playlistId);
                    localStorage.setItem(`playlist_cover_${playlistId}`, compressedData);
                    console.log('Imagen guardada en localStorage');
                } catch (e) {
                    console.error('Error al guardar en localStorage:', e);
                    alert('No hay suficiente espacio para guardar la imagen. Intenta con una imagen más pequeña.');
                }
            });
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        currentCover.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    let songs = @json($songs ?: []);

    // Función para actualizar el input hidden
    function updateSongsInput() {
        document.getElementById('songs-input').value = JSON.stringify(songs);
    }

    // Event listener para eliminar canciones
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-song')) {
            const songIndex = parseInt(e.target.dataset.songId);
            songs.splice(songIndex, 1);
            e.target.closest('.song-item').remove();
            updateSongsInput();
        }
    });

    // Cargar imagen desde localStorage si existe
    const savedCover = localStorage.getItem(`playlist_cover_${playlistId}`);
    if (savedCover) {
        console.log('Cargando imagen guardada para playlist', playlistId);
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const currentCover = document.getElementById('current-cover');
        
        previewImg.src = savedCover;
        preview.style.display = 'block';
        currentCover.style.display = 'none';
    }

    // Guardar imagen en localStorage cuando se envía el formulario
    document.querySelector('form').addEventListener('submit', function() {
        const fileInput = document.getElementById('cover');
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        console.log('Formulario enviado');
        if (preview.style.display !== 'none' && previewImg.src) {
            console.log('Guardando imagen en localStorage en el submit');
            try {
                localStorage.setItem(`playlist_cover_${playlistId}`, previewImg.src);
            } catch (e) {
                console.error('Error al guardar:', e);
            }
        }
    });

    updateSongsInput();
});
</script>
@endsection