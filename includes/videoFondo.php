<?php
// Lista de videos
$videos = [
    "screenCode" => "../video/screenCode.mp4",
    "screenCode2" => "../video/screenCode2.mp4",
    "screenCode3" => "../video/screenCode3.mp4",
    "MU-TH-UR 6000 Matrix" => "../video/screenCodeMatrixMod.mp4",
    "Matrix" => "../video/screenCodeMatrix.mp4"
];

// Video predeterminado en caso de que no exista en localStorage
$defaultVideo = $videos["screenCode"];
?>

<video autoplay muted loop id="screenCode">
    <source src="<?php echo $defaultVideo; ?>" type="video/mp4">
    MU-TH-UR 6000 está experimentando serios problemas para reproducir el video.
</video>

<script>
    // Cargar video desde localStorage o usar el predeterminado
    const videoElement = document.getElementById('screenCode');
    const savedVideo = localStorage.getItem('selectedVideo');
    if (savedVideo) {
        videoElement.src = savedVideo;
        videoElement.load();
    } else {
        // Guardar el video predeterminado si no hay nada en localStorage
        localStorage.setItem('selectedVideo', "<?php echo $defaultVideo; ?>");
    }

    
</script>