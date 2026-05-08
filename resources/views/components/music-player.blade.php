<div id="music-player" class="card shadow-lg music-player-floating d-none">
    <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center py-2 px-3">
        <span class="small fw-bold"><i class="bi bi-music-note-beamed"></i> Vibely Mini</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-link text-white p-0"
                onclick="window.location.href='{{ route('player') }}'"><i
                    class="bi bi-arrows-angle-expand"></i></button>
            <button type="button" class="btn-close btn-close-white" id="close-player"
                style="font-size: 0.7rem;"></button>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3 mb-2">
            <img id="player-cover" src="https://picsum.photos/seed/music/60/60" class="rounded shadow-sm" alt="Cover"
                style="width: 50px; height: 50px; object-fit: cover;">
            <div class="overflow-hidden">
                <h6 id="player-title" class="mb-0 text-truncate small fw-bold">No sonando</h6>
                <p id="player-artist" class="text-muted mb-0 text-truncate" style="font-size: 0.7rem;">Selecciona una
                    canción</p>
            </div>
        </div>

        <audio id="audio-element" src=""></audio>

        <div class="d-flex align-items-center gap-2 mb-2">
            <span id="current-time" class="text-muted" style="font-size: 0.65rem;">0:00</span>
            <input type="range" class="form-range form-range-sm" id="seek-slider" value="0" step="1" min="0"
                style="height: 0.2rem;">
            <span id="total-duration" class="text-muted" style="font-size: 0.65rem;">0:00</span>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-3">
            <button class="btn btn-sm btn-link text-purple p-0" id="prev-btn"><i
                    class="bi bi-skip-backward-fill"></i></button>
            <button class="btn btn-purple btn-sm rounded-circle d-flex align-items-center justify-content-center"
                id="play-pause-btn" style="width: 32px; height: 32px;">
                <i class="bi bi-play-fill" id="play-icon"></i>
            </button>
            <button class="btn btn-sm btn-link text-purple p-0" id="next-btn"><i
                    class="bi bi-skip-forward-fill"></i></button>
        </div>
    </div>
</div>

<style>
    .music-player-floating {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 260px;
        z-index: 2000;
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(111, 66, 193, 0.2) !important;
    }

    .btn-purple {
        background-color: #6f42c1;
        color: white;
        border: none;
    }

    .btn-purple:hover {
        background-color: #59359a;
        color: white;
    }

    .text-purple {
        color: #6f42c1;
    }

    #seek-slider::-webkit-slider-thumb {
        width: 10px;
        height: 10px;
        background: #6f42c1;
    }
</style>

<script>
    (function () {
        const SONGS = [
            { title: "Summer Vibes", artist: "Lofi Girl", src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3", cover: "https://picsum.photos/seed/song1/100/100" },
            { title: "Midnight City", artist: "Electronic Dreams", src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3", cover: "https://picsum.photos/seed/song2/100/100" },
            { title: "Golden Hour", artist: "Acoustic Session", src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3", cover: "https://picsum.photos/seed/song3/100/100" }
        ];

        let state = {
            index: parseInt(localStorage.getItem('vibely_index')) || 0,
            time: parseFloat(localStorage.getItem('vibely_time')) || 0,
            playing: localStorage.getItem('vibely_playing') === 'true',
            visible: localStorage.getItem('vibely_visible') === 'true'
        };

        document.addEventListener('DOMContentLoaded', function () {
            const playerEl = document.getElementById('music-player');
            const audio = document.getElementById('audio-element');
            const playIcon = document.getElementById('play-icon');
            const titleEl = document.getElementById('player-title');
            const artistEl = document.getElementById('player-artist');
            const coverEl = document.getElementById('player-cover');
            const seekSlider = document.getElementById('seek-slider');
            const currentTimeEl = document.getElementById('current-time');
            const totalDurationEl = document.getElementById('total-duration');

            function saveState() {
                localStorage.setItem('vibely_index', state.index);
                localStorage.setItem('vibely_time', audio.currentTime);
                localStorage.setItem('vibely_playing', !audio.paused);
                localStorage.setItem('vibely_visible', !playerEl.classList.contains('d-none'));
            }

            function loadSong(index, seekTo = 0) {
                const song = SONGS[index];
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

            // Sync visibility
            if (state.visible) playerEl.classList.remove('d-none');

            loadSong(state.index, state.time);

            if (state.playing) {
                // Browser policies might block autoplay without user interaction
                // We attempt to play, but catch error
                audio.play().then(() => {
                    playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                }).catch(() => {
                    state.playing = false;
                    saveState();
                });
            }

            // Controls
            document.getElementById('play-pause-btn').onclick = () => {
                if (audio.paused) {
                    audio.play();
                    playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                } else {
                    audio.pause();
                    playIcon.classList.replace('bi-pause-fill', 'bi-play-fill');
                }
                saveState();
            };

            document.getElementById('next-btn').onclick = () => {
                state.index = (state.index + 1) % SONGS.length;
                loadSong(state.index);
                audio.play();
                playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                saveState();
            };

            document.getElementById('prev-btn').onclick = () => {
                state.index = (state.index - 1 + SONGS.length) % SONGS.length;
                loadSong(state.index);
                audio.play();
                playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
                saveState();
            };

            document.getElementById('close-player').onclick = () => {
                playerEl.classList.add('d-none');
                saveState();
            };

            audio.ontimeupdate = () => {
                seekSlider.value = audio.currentTime;
                currentTimeEl.textContent = formatTime(audio.currentTime);
                // Save time occasionally
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

            audio.onended = () => document.getElementById('next-btn').click();

            // Global access
            window.toggleMusicPlayer = function () {
                playerEl.classList.toggle('d-none');
                saveState();
            };

            // Listen for storage changes (to sync across tabs if needed)
            window.addEventListener('storage', (e) => {
                if (e.key === 'vibely_action' && e.newValue === 'toggle') {
                    window.toggleMusicPlayer();
                }
            });
        });
    })();
</script>