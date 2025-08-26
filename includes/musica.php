<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div id="audio-control" class="audio-container">
    <!-- Título principal -->
    <div id="audio-name" class="audio-header">
        <h6>CONTROL DE AUDIO</h6>
    </div>
    <!-- Botones de control -->
    <div id="audio-buttons" class="audio-controls">
        <button onclick="prevTrack()" id="prev" class="audio-btn">
            <i class="fas fa-backward"></i>
        </button>
        <button onclick="togglePlayPause()" id="playPause" class="audio-btn">
            <i class="fas fa-play"></i>
        </button>
        <button onclick="nextTrack()" id="next" class="audio-btn">
            <i class="fas fa-forward"></i>
        </button>
    </div>
    <!-- Barra de progreso -->
    <div id="progress-bar" class="progress-container">
        <div id="progress" class="progress-bar"></div>
    </div>
    <!-- Título de la canción -->
    <p id="audio-title" class="audio-title">Título de la Canción</p>
</div>

<audio id="audio-player" style="display: none;">
    <source id="audio-source" src="../audio/EnteringNostromo.mp3" type="audio/mp3">
    MU-TH-UR 6000 está experimentando serios problemas para reproducir el audio.
</audio>

<script>
    // Lista de pistas con los nombres
    const tracks = [
        { src: "../audio/MU-TH-UR 6000.mp3", title: "1. ARRANQUE DEL SISTEMA" },
        { src: "../audio/MU-TH-UR 6000.mp3", title: "2. MU-TH-UR 6000" },
        { src: "../audio/USCSS - NOSTROMO.mp3", title: "3. USCSS - NOSTROMO" },
        { src: "../audio/USCSS - PALADIO.mp3", title: "4. USCSS - PALADIO" },
        { src: "../audio/WHEELHOUSE.mp3", title: "5. PUENTE DE MANDO" },
        { src: "../audio/Weyland.mp3", title: "6. Weyland Yutani" },
        { src: "../audio/Los Ingenieros.mp3", title: "7. Los Ingenieros" }
    ];

    let currentTrackIndex = localStorage.getItem('currentTrackIndex') ? parseInt(localStorage.getItem('currentTrackIndex')) : 0;
    let isPlaying = localStorage.getItem('isPlaying') === 'true';
    let currentTime = localStorage.getItem('currentTime') ? parseFloat(localStorage.getItem('currentTime')) : 0;
    const audioPlayer = document.getElementById("audio-player");
    const playPauseButton = document.getElementById("playPause").getElementsByTagName('i')[0];
    const audioTitle = document.getElementById("audio-title");

    audioPlayer.addEventListener('ended', () => { //AL ACABAR REPRODUCIR LA SIGUIENTE AUTOMATICAMENTE
        nextTrack();
    })

    function loadTrack(index) {
        // Si cambiamos de pista, no restablecemos la posición a cero
        if (audioPlayer.src !== tracks[index].src) {
            audioPlayer.src = tracks[index].src;
            audioPlayer.load();
            audioTitle.textContent = tracks[index].title;

            // No reiniciar la canción a 0, sino continuar desde la última posición guardada
            if (isPlaying) {
                audioPlayer.play();
            }

            // Actualizar el icono de play/pause
            playPauseButton.classList.remove("fa-play");
            playPauseButton.classList.add(isPlaying ? "fa-pause" : "fa-play");
        }
        else {
            // Continuar desde la posición guardada sin reiniciar
            audioPlayer.currentTime = currentTime;
        }
    }

    function togglePlayPause() {
        if (audioPlayer.paused) {
            audioPlayer.play();
            isPlaying = true;
            playPauseButton.classList.remove("fa-play");
            playPauseButton.classList.add("fa-pause");
        } else {
            audioPlayer.pause();
            isPlaying = false;
            playPauseButton.classList.remove("fa-pause");
            playPauseButton.classList.add("fa-play");
        }

        // Guardamos el estado de la canción en localStorage
        localStorage.setItem('isPlaying', isPlaying);
    }

    // Guardar la posición de la canción cuando se cambia
    audioPlayer.ontimeupdate = function () {
        localStorage.setItem('currentTime', audioPlayer.currentTime);
    };


    function nextTrack() {
        currentTrackIndex = (currentTrackIndex + 1) % tracks.length;
        loadTrack(currentTrackIndex);
        localStorage.setItem('currentTrackIndex', currentTrackIndex);
    }

    function prevTrack() {
        currentTrackIndex = (currentTrackIndex - 1 + tracks.length) % tracks.length;
        loadTrack(currentTrackIndex);
        localStorage.setItem('currentTrackIndex', currentTrackIndex);
    }

    window.onload = function () {
        loadTrack(currentTrackIndex);

        // Si la canción estaba reproduciéndose, reiniciamos el estado
        if (isPlaying) {
            audioPlayer.play();
        }

        audioPlayer.currentTime = currentTime;
    };

    const progressBar = document.getElementById('progress-bar');
    const progress = document.getElementById('progress');


    // Actualiza la barra de progreso en tiempo real
    audioPlayer.ontimeupdate = function () {
        const percentage = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        progress.style.width = percentage + '%';
        // Guardamos la posición actual en localStorage
        localStorage.setItem('currentTime', audioPlayer.currentTime);
    };

    // Click en la barra de progreso
    progressBar.addEventListener('click', function (e) {
        const rect = progressBar.getBoundingClientRect();
        const offsetX = e.clientX - rect.left;
        const percentage = offsetX / progressBar.offsetWidth;
        audioPlayer.currentTime = percentage * audioPlayer.duration;
    });
</script>