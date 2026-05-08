@extends('layouts.app')

@section('content')
    <div class="row">
        <!-- Columna Izquierda: Reproductor en Grande -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-lg text-white overflow-hidden rounded-4"
                style="background: var(--vibely-gradient); min-height: 500px;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="mb-4 text-center">
                        <img id="large-player-cover" src="https://picsum.photos/seed/music_large/300/300"
                            class="rounded-4 shadow-lg mb-4 pulse-animation" alt="Cover"
                            style="width: 250px; height: 250px; object-fit: cover; border: 5px solid rgba(255,255,255,0.2);">
                        <h2 id="large-player-title" class="fw-bold mb-1 text-truncate" style="max-width: 400px;">Selecciona
                            una canción</h2>
                        <p id="large-player-artist" class="opacity-75 fs-5">Vibely Premium</p>
                    </div>

                    <audio id="large-audio-element" src=""></audio>

                    <div class="w-75 mb-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span id="large-current-time" class="small opacity-75">0:00</span>
                            <input type="range" class="form-range custom-range" id="large-seek-slider" value="0" step="1"
                                min="0">
                            <span id="large-total-duration" class="small opacity-75">0:00</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-4">
                        <button class="btn btn-link text-white p-0 opacity-75 hover-opacity-100"
                            style="font-size: 1.5rem;"><i class="bi bi-shuffle"></i></button>
                        <button class="btn btn-link text-white p-0" style="font-size: 2rem;" id="large-prev-btn"><i
                                class="bi bi-skip-backward-circle-fill"></i></button>
                        <button
                            class="btn btn-white text-purple rounded-circle d-flex align-items-center justify-content-center shadow"
                            style="width: 80px; height: 80px; font-size: 2.5rem;" id="large-play-pause-btn">
                            <i class="bi bi-play-fill" id="large-play-icon"></i>
                        </button>
                        <button class="btn btn-link text-white p-0" style="font-size: 2rem;" id="large-next-btn"><i
                                class="bi bi-skip-forward-circle-fill"></i></button>
                        <button class="btn btn-link text-white p-0 opacity-75 hover-opacity-100"
                            style="font-size: 1.5rem;"><i class="bi bi-repeat"></i></button>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-0 text-center pb-4">
                    <button class="btn btn-light btn-sm rounded-pill px-4 text-purple fw-bold shadow-sm"
                        onclick="miniaturizePlayer()">
                        <i class="bi bi-fullscreen-exit me-2"></i> Miniaturizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Selección de Playlists -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <ul class="nav nav-pills mb-4 gap-2" id="playlistTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill px-3" id="mine-tab" data-bs-toggle="pill"
                                data-bs-target="#mine">Tus Listas</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill px-3" id="following-tab" data-bs-toggle="pill"
                                data-bs-target="#following">Siguiendo</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill px-3" id="foryou-tab" data-bs-toggle="pill"
                                data-bs-target="#foryou">Para ti</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="playlistTabsContent">
                        <!-- Tus Listas -->
                        <div class="tab-pane fade show active" id="mine">
                            <div class="list-group list-group-flush" id="mine-list"></div>
                        </div>
                        <!-- Siguiendo -->
                        <div class="tab-pane fade" id="following">
                            <div class="list-group list-group-flush" id="following-list"></div>
                        </div>
                        <!-- Para ti -->
                        <div class="tab-pane fade" id="foryou">
                            <div class="list-group list-group-flush" id="foryou-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-white {
            background: white;
            border: none;
            transition: transform 0.2s ease;
        }

        .btn-white:hover {
            transform: scale(1.1);
            background: #f8f9fa;
            color: var(--vibely-purple);
        }

        .custom-range::-webkit-slider-thumb {
            background: white;
            border: none;
        }

        .custom-range::-webkit-slider-runnable-track {
            background: rgba(255, 255, 255, 0.3);
            height: 4px;
            border-radius: 2px;
        }

        .hover-opacity-100:hover {
            opacity: 1 !important;
        }

        .pulse-animation {
            animation: pulse 4s infinite ease-in-out;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }

            50% {
                transform: scale(1.02);
                box-shadow: 0 0 30px 10px rgba(255, 255, 255, 0.1);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }
        }

        .nav-pills .nav-link.active {
            background-color: var(--vibely-purple);
        }

        .nav-pills .nav-link {
            color: var(--vibely-purple);
            font-weight: bold;
        }
    </style>
@endsection

@push('scripts')
    <script>
        (function () {
            const DEFAULT_SONGS = [
                { title: "Summer Vibes", artist: "Lofi Girl", src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3", cover: "https://picsum.photos/seed/song1/300/300" },
                { title: "Midnight City", artist: "Electronic Dreams", src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3", cover: "https://picsum.photos/seed/song2/300/300" },
                { title: "Golden Hour", artist: "Acoustic Session", src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3", cover: "https://picsum.photos/seed/song3/300/300" }
            ];

            const SONG_LIBRARY = {
                "Walking on Sunshine": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3", cover: "https://picsum.photos/seed/walkingonsunshine/300/300" },
                "Happy": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3", cover: "https://picsum.photos/seed/happy/300/300" },
                "Good Vibrations": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3", cover: "https://picsum.photos/seed/goodvibrations/300/300" },
                "Dancing Queen": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3", cover: "https://picsum.photos/seed/dancingqueen/300/300" },
                "Can't Stop the Feeling!": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3", cover: "https://picsum.photos/seed/cantstopthefeeling/300/300" },
                "Uptown Funk": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3", cover: "https://picsum.photos/seed/uptownfunk/300/300" },
                "September": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3", cover: "https://picsum.photos/seed/september/300/300" },
                "Weightless": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3", cover: "https://picsum.photos/seed/weightless/300/300" },
                "Claire de Lune": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3", cover: "https://picsum.photos/seed/clairedelune/300/300" },
                "Riverside": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3", cover: "https://picsum.photos/seed/riverside/300/300" },
                "Holocene": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3", cover: "https://picsum.photos/seed/holocene/300/300" },
                "River Flows in You": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3", cover: "https://picsum.photos/seed/riverflowsinyou/300/300" },
                "Saturn": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3", cover: "https://picsum.photos/seed/saturn/300/300" },
                "Bloom": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3", cover: "https://picsum.photos/seed/bloom/300/300" },
                "Someone Like You": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-15.mp3", cover: "https://picsum.photos/seed/someonelikeyou/300/300" },
                "Stay With Me": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-16.mp3", cover: "https://picsum.photos/seed/staywithme/300/300" },
                "The Night We Met": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-17.mp3", cover: "https://picsum.photos/seed/thenightwemet/300/300" },
                "Skinny Love": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-18.mp3", cover: "https://picsum.photos/seed/skinnylove/300/300" },
                "Fix You": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-19.mp3", cover: "https://picsum.photos/seed/fixyou/300/300" },
                "All I Want": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-20.mp3", cover: "https://picsum.photos/seed/alliwant/300/300" },
                "Hurt": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-21.mp3", cover: "https://picsum.photos/seed/hurt/300/300" },
                "Eye of the Tiger": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-22.mp3", cover: "https://picsum.photos/seed/eyeofthetiger/300/300" },
                "Thunderstruck": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-23.mp3", cover: "https://picsum.photos/seed/thunderstruck/300/300" },
                "Don't Stop Me Now": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-24.mp3", cover: "https://picsum.photos/seed/dontstopmenow/300/300" },
                "Can't Hold Us": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-25.mp3", cover: "https://picsum.photos/seed/cantholdus/300/300" },
                "Titanium": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-26.mp3", cover: "https://picsum.photos/seed/titanium/300/300" },
                "Stronger": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-27.mp3", cover: "https://picsum.photos/seed/stronger/300/300" },
                "Levels": { src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-28.mp3", cover: "https://picsum.photos/seed/levels/300/300" }
            };

            @php
            $playlistData = $playlists->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'title' => $playlist->title,
                    'author' => $playlist->user->name,
                    'songs' => is_string($playlist->songs) ? json_decode($playlist->songs, true) ?? [] : (is_array($playlist->songs) ? $playlist->songs : []),
                    'cover' => $playlist->cover,
                ];
            })->values();
            @endphp

            const PLAYLISTS = @json($playlistData);

            const EMOTION_DATA = {
                'Felicidad': { color: '#ffc107', emoji: '😊' },
                'Calma': { color: '#0dcaf0', emoji: '😌' },
                'Tristeza': { color: '#6c757d', emoji: '😢' },
                'Energía': { color: '#dc3545', emoji: '⚡' }
            };

            let currentTrackList = DEFAULT_SONGS;
            let currentPlaylist = null;

            let state = {
                index: parseInt(localStorage.getItem('vibely_index')) || 0,
                time: parseFloat(localStorage.getItem('vibely_time')) || 0,
                playing: localStorage.getItem('vibely_playing') === 'true'
            };

            window.miniaturizePlayer = function () {
                localStorage.setItem('vibely_visible', 'true');
                window.location.href = "{{ route('home') }}";
            };

            document.addEventListener('DOMContentLoaded', function () {
                const audio = document.getElementById('large-audio-element');
                const playIcon = document.getElementById('large-play-icon');
                const titleEl = document.getElementById('large-player-title');
                const artistEl = document.getElementById('large-player-artist');
                const coverEl = document.getElementById('large-player-cover');
                const seekSlider = document.getElementById('large-seek-slider');
                const currentTimeEl = document.getElementById('large-current-time');
                const totalDurationEl = document.getElementById('large-total-duration');

                function saveState() {
                    localStorage.setItem('vibely_index', state.index);
                    localStorage.setItem('vibely_time', audio.currentTime);
                    localStorage.setItem('vibely_playing', !audio.paused);
                }

                function prepareTrack(song) {
                    // Si la canción tiene preview_url, úsala directamente
                    if (song.preview_url) {
                        return {
                            title: song.title || 'Sin título',
                            artist: song.artist || 'Artista desconocido',
                            src: song.preview_url,
                            cover: song.album?.images?.[0]?.url || `https://picsum.photos/seed/${encodeURIComponent((song.title || 'song').replace(/[^a-zA-Z0-9]/g, ''))}/300/300`
                        };
                    }
                    
                    // Si no tiene preview_url, busca en la librería o usa valores por defecto
                    const libraryTrack = SONG_LIBRARY[song.title] || {
                        src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3",
                        cover: `https://picsum.photos/seed/${encodeURIComponent((song.title || 'song').replace(/[^a-zA-Z0-9]/g, ''))}/300/300`
                    };
                    return {
                        title: song.title || 'Sin título',
                        artist: song.artist || 'Artista desconocido',
                        src: libraryTrack.src,
                        cover: libraryTrack.cover
                    };
                }

                function loadSong(index, seekTo = 0) {
                    const song = currentTrackList[index] || currentTrackList[0];
                    audio.src = song.src;
                    titleEl.textContent = song.title;
                    artistEl.textContent = song.artist;
                    coverEl.src = song.cover;
                    audio.currentTime = seekTo;
                    state.index = index;
                }

                function formatTime(s) {
                    if (isNaN(s)) return "0:00";
                    const m = Math.floor(s / 60);
                    const r = Math.floor(s % 60);
                    return `${m}:${r < 10 ? '0' : ''}${r}`;
                }

                function selectPlaylist(playlist) {
                    if (playlist.songs && playlist.songs.length) {
                        currentTrackList = playlist.songs.map(prepareTrack);
                    } else {
                        currentTrackList = DEFAULT_SONGS;
                    }
                    currentPlaylist = playlist;
                    state.index = 0;
                    loadSong(state.index);
                    audio.play();
                    playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                    saveState();
                }

                loadSong(state.index, state.time);

                if (state.playing) {
                    audio.play().catch(() => { state.playing = false; saveState(); });
                    playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                }

                document.getElementById('large-play-pause-btn').onclick = () => {
                    if (audio.paused) {
                        audio.play();
                        playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                    } else {
                        audio.pause();
                        playIcon.classList.replace('bi-pause-fill', 'bi-play-fill');
                    }
                    saveState();
                };

                document.getElementById('large-next-btn').onclick = () => {
                    state.index = (state.index + 1) % currentTrackList.length;
                    loadSong(state.index);
                    audio.play();
                    playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                    saveState();
                };

                document.getElementById('large-prev-btn').onclick = () => {
                    state.index = (state.index - 1 + currentTrackList.length) % currentTrackList.length;
                    loadSong(state.index);
                    audio.play();
                    playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                    saveState();
                };

                audio.ontimeupdate = () => {
                    seekSlider.value = audio.currentTime;
                    currentTimeEl.textContent = formatTime(audio.currentTime);
                    if (Math.floor(audio.currentTime) % 2 === 0) saveState();
                };

                audio.onloadedmetadata = () => {
                    seekSlider.max = audio.duration;
                    totalDurationEl.textContent = formatTime(audio.duration);
                };

                seekSlider.oninput = () => {
                    audio.currentTime = seekSlider.value;
                    saveState();
                };

                audio.onended = () => document.getElementById('large-next-btn').click();

                // Playlists rendering
                const playlistData = {
                    mine: PLAYLISTS.map(playlist => ({
                        title: playlist.title,
                        author: playlist.author,
                        songs: playlist.songs,
                        cover: playlist.cover,
                    })),
                    following: [
                        // Aquí irían las listas de personas a las que sigue
                        @foreach(\App\Models\Playlist::where('user_id', '!=', Auth::id())->limit(2)->get() as $p)
                            { title: '{{ $p->title }}', author: '{{ $p->user->name }}', index: 0, color: '#fd7e14', songs: [] },
                        @endforeach
                    ],
                    foryou: [
                        { title: 'Discover Weekly', author: 'Vibely AI', index: 0, color: '#0dcaf0', songs: [] }
                    ]
                };

                function renderList(targetId, list) {
                    const container = document.getElementById(targetId);
                    if (!container) return;
                    container.innerHTML = '';
                    list.forEach(item => {
                        const el = document.createElement('a');
                        el.href = '#';
                        el.className = 'list-group-item list-group-item-action d-flex align-items-center gap-3 border-0 py-3 px-3 mb-2 rounded-3';
                        
                        let coverHtml = '';
                        if (item.cover) {
                            if (item.cover.startsWith('auto:')) {
                                const mood = item.cover.split(':')[1];
                                const emotion = EMOTION_DATA[mood] || { color: '#6f42c1', emoji: '🎵' };
                                coverHtml = `<div style="width: 40px; height: 40px; background: ${emotion.color}; border: 2px solid rgba(255,255,255,0.3);" class="rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white fs-5">${emotion.emoji}</div>`;
                            } else if (item.cover.startsWith('http')) {
                                coverHtml = `<div style="width: 40px; height: 40px; background-image: url('${item.cover}'); background-size: cover; background-position: center; border: 2px solid rgba(255,255,255,0.3);" class="rounded-circle shadow-sm"></div>`;
                            } else {
                                coverHtml = `
                                    <img src="/storage/${item.cover}" 
                                        style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);" 
                                        class="rounded-circle shadow-sm"
                                        onerror="this.onerror=null; this.outerHTML = \`
                                            <div style='width: 40px; height: 40px; background: linear-gradient(135deg, #6f42c1, #a370f7); border: 2px solid rgba(255,255,255,0.3);' class='rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white'>
                                                <i class='bi bi-music-note'></i>
                                            </div>\`;
                                        ">
                                `;
                            }
                        } else {
                            coverHtml = `<div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6f42c1, #a370f7); border: 2px solid rgba(255,255,255,0.3);" class="rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white"><i class="bi bi-music-note"></i></div>`;
                        }
                        
                        el.innerHTML = `
                            ${coverHtml}
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold small">${item.title}</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">${item.author} · ${item.songs?.length || 0} canciones</small>
                            </div>
                        `;
                        el.onclick = (e) => {
                            e.preventDefault();
                            if (item.songs && item.songs.length) {
                                selectPlaylist(item);
                            } else {
                                state.index = item.index;
                                loadSong(state.index);
                                audio.play();
                                playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                                saveState();
                            }
                        };
                        container.appendChild(el);
                    });
                }

                renderList('mine-list', playlistData.mine);
                renderList('following-list', playlistData.following);
                renderList('foryou-list', playlistData.foryou);
            });
        })();
    </script>
@endpush
