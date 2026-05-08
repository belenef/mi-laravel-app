@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            @if($playlist->cover && file_exists(storage_path('app/public/' . $playlist->cover)))
                                <img src="{{ asset('storage/' . $playlist->cover) }}?t={{ $playlist->updated_at->timestamp }}" alt="Cover" class="rounded" style="width: 80px; height: 80px; object-fit: cover; object-position: center;">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <span class="text-white fs-2">{{ $playlist->mood === 'happy' ? '😊' : ($playlist->mood === 'sad' ? '😢' : ($playlist->mood === 'energetic' ? '⚡' : '🎵')) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="card-title mb-1">{{ $playlist->title }}</h2>
                            <p class="text-muted mb-1">Por {{ $playlist->user->name }}</p>
                            <p class="text-muted mb-0">{{ $playlist->mood }} • {{ count(json_decode($playlist->songs ?? '[]', true) ?? []) }} canciones</p>
                        </div>
                    </div>

                    @if($playlist->description)
                        <p class="card-text">{{ $playlist->description }}</p>
                    @endif

                    @if(Auth::check() && Auth::id() === $playlist->user_id)
                        <div class="mb-3">
                            <a href="{{ route('playlists.edit', $playlist) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                            <form method="POST" action="{{ route('playlists.destroy', $playlist) }}" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta playlist?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                            </form>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>Canciones</h5>
                        <div id="playlist-songs">
                            @php $songs = json_decode($playlist->songs ?? '[]', true) ?? []; @endphp
                            @if($songs)
                                @foreach($songs as $index => $song)
                                    <div class="d-flex align-items-center justify-content-between p-2 border-bottom song-item" data-song='{{ json_encode($song) }}'>
                                        <div class="d-flex align-items-center">
                                            <!-- <button class="btn btn-sm btn-outline-secondary me-3 play-btn" data-index="{{ $index }}">
                                                <i class="fas fa-play"></i>
                                            </button> -->
                                            <div>
                                                <div class="fw-bold">{{ $song['title'] ?? 'Sin título' }}</div>
                                                <div class="text-muted small">{{ $song['artist'] ?? 'Artista desconocido' }}</div>
                                            </div>
                                        </div>
                                        <!-- <small class="text-muted">{{ $song['duration'] ?? 'N/A' }}</small> -->
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No hay canciones en esta playlist.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reproductor</h5>
                    <div id="player-container">
                        <p class="text-muted">Selecciona una canción para reproducirla</p>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar imagen desde localStorage o sessionStorage
    const playlistId = {{ $playlist->id }};
    const savedCover = localStorage.getItem(`playlist_cover_${playlistId}`);
    
    // Verificar si hay una imagen temporal en sessionStorage (desde creación)
    const tempCover = sessionStorage.getItem('new_playlist_cover');
    
    console.log('=== PLAYLIST DETAIL SCRIPT ===');
    console.log('Playlist ID:', playlistId);
    console.log('Portada guardada en localStorage:', savedCover ? 'Sí' : 'No');
    console.log('Portada temporal en sessionStorage:', tempCover ? 'Sí' : 'No');
    
    let imageToUse = null;
    
    // Si hay imagen temporal, guardarla en localStorage con el ID correcto
    if (tempCover && !savedCover) {
        console.log('Moviendo portada temporal a localStorage');
        try {
            localStorage.setItem(`playlist_cover_${playlistId}`, tempCover);
            sessionStorage.removeItem('new_playlist_cover');
            imageToUse = tempCover;
            console.log('Portada temporal movida a localStorage exitosamente');
        } catch (e) {
            console.error('Error al mover portada:', e);
        }
    } else if (savedCover) {
        imageToUse = savedCover;
        console.log('Usando portada guardada desde localStorage');
    }
    
    // Reemplazar la imagen/emoji con la portada guardada
    if (imageToUse) {
        const coverDiv = document.querySelector('.card-body .me-3');
        if (coverDiv) {
            console.log('Reemplazando portada en la página');
            coverDiv.innerHTML = `<img src="${imageToUse}" alt="Cover" class="rounded" style="width: 80px; height: 80px; object-fit: cover; object-position: center;">`;
            console.log('Portada reemplazada exitosamente');
        } else {
            console.log('Contenedor .me-3 no encontrado');
        }
    } else {
        console.log('No hay portada para mostrar');
    }

    // Reproductor simple para las canciones de la playlist
    const playButtons = document.querySelectorAll('.play-btn');
    const playerContainer = document.getElementById('player-container');
    let currentAudio = null;

    playButtons.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            const songItem = this.closest('.song-item');
            const songData = JSON.parse(songItem.dataset.song);
            
            // Detener audio anterior si existe
            if (currentAudio) {
                currentAudio.pause();
                currentAudio.currentTime = 0;
                // Resetear todos los botones
                document.querySelectorAll('.play-btn').forEach(b => {
                    b.innerHTML = '<i class="fas fa-play"></i>';
                });
            }

            // Actualizar el reproductor
            playerContainer.innerHTML = `
                <div class="text-center">
                    <h6>${songData.title ?? 'Sin título'}</h6>
                    <p class="text-muted">${songData.artist ?? 'Artista desconocido'}</p>
                    <div class="mt-3">
                        <button id="play-pause-btn" class="btn btn-primary">
                            <i class="fas fa-play"></i> Reproducir
                        </button>
                    </div>
                </div>
            `;

            // Si hay URL de preview, reproducir
            if (songData.preview_url) {
                currentAudio = new Audio(songData.preview_url);
                
                const playPauseBtn = document.getElementById('play-pause-btn');
                let isPlaying = false;
                
                playPauseBtn.addEventListener('click', function() {
                    if (isPlaying) {
                        currentAudio.pause();
                        this.innerHTML = '<i class="fas fa-play"></i> Reproducir';
                        isPlaying = false;
                    } else {
                        currentAudio.play();
                        this.innerHTML = '<i class="fas fa-pause"></i> Pausar';
                        isPlaying = true;
                    }
                });
                
                currentAudio.addEventListener('ended', () => {
                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i> Reproducir';
                    isPlaying = false;
                });
            }
        });
    });
});
</script>
@endsection