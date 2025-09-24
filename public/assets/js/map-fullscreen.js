document.addEventListener('DOMContentLoaded', function() {
    const fullscreenButton = document.getElementById('fullscreenButton');
    const mapContainer = document.getElementById('mapContainer');
    const map = document.getElementById('map');
    const fullscreenIcon = fullscreenButton.querySelector('i');
    let isFullscreen = false;

    // Toggle fullscreen
    function toggleFullscreen() {
        if (!isFullscreen) {
            // Enter fullscreen
            mapContainer.classList.add('fullscreen');
            document.body.style.overflow = 'hidden';
            fullscreenIcon.classList.remove('fa-expand');
            fullscreenIcon.classList.add('fa-compress');
            isFullscreen = true;
            
            // Trigger map resize after a small delay to ensure the container is fully expanded
            setTimeout(() => {
                if (window.google && window.google.maps && window.mapInstance) {
                    google.maps.event.trigger(window.mapInstance, 'resize');
                    // Optional: Re-center the map if needed
                    if (window.mapInstance.center) {
                        const center = window.mapInstance.getCenter();
                        window.mapInstance.setCenter(center);
                    }
                }
            }, 100);
        } else {
            // Exit fullscreen
            mapContainer.classList.remove('fullscreen');
            document.body.style.overflow = '';
            fullscreenIcon.classList.remove('fa-compress');
            fullscreenIcon.classList.add('fa-expand');
            isFullscreen = false;
            
            // Trigger map resize after a small delay to ensure the container is fully collapsed
            setTimeout(() => {
                if (window.google && window.google.maps && window.mapInstance) {
                    google.maps.event.trigger(window.mapInstance, 'resize');
                    // Optional: Re-center the map if needed
                    if (window.mapInstance.center) {
                        const center = window.mapInstance.getCenter();
                        window.mapInstance.setCenter(center);
                    }
                }
            }, 100);
        }
    }

    // Add click event listener
    if (fullscreenButton) {
        fullscreenButton.addEventListener('click', toggleFullscreen);
    }

    // Handle escape key to exit fullscreen
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isFullscreen) {
            toggleFullscreen();
        }
    });
});
