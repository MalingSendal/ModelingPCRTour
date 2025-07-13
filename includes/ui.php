<?php
function setupUI() {
    echo '<div id="sidePanel">
        <h2>Teleport Locations</h2>
        <button class="teleport-btn" data-location="0,2,5">A - Entrance</button>
        <button class="teleport-btn" data-location="5,2,0">B - Hallway</button>
        <button class="teleport-btn" data-location="-5,2,0">C - Classroom</button>
        <button class="teleport-btn" data-location="0,2,-5">D - Courtyard</button>
        <button class="teleport-btn" id="backToMenuBtn" style="background-color:#b33;">⬅ Back to Main Menu</button>
    </div>
    
    <div id="menuToggle">☰</div>
    
    <div id="coordinates">X: 0.00, Y: 0.00, Z: 0.00</div>';

    echo '<script>
    // UI controls using global objects
    const sidePanel = document.getElementById("sidePanel");
    const menuToggle = document.getElementById("menuToggle");
    const backToMenuBtn = document.getElementById("backToMenuBtn");
    
    menuToggle.addEventListener("click", () => {
        sidePanel.classList.toggle("open");
    });

    if (backToMenuBtn) {
        backToMenuBtn.addEventListener("click", () => {
            window.location.href = "index.php";
        });
    }

    window.updateCoordinates = function() {
        const coordDisplay = document.getElementById("coordinates");
        coordDisplay.textContent = `X: ${viewerCamera.position.x.toFixed(2)}, Y: ${viewerCamera.position.y.toFixed(2)}, Z: ${viewerCamera.position.z.toFixed(2)}`;
    };

    window.addEventListener("resize", () => {
        viewerCamera.aspect = window.innerWidth / window.innerHeight;
        viewerCamera.updateProjectionMatrix();
        viewerRenderer.setSize(window.innerWidth, window.innerHeight);
    });
    </script>';
}
?>