<?php
function setupTeleport() {
    echo '<script>
    document.querySelectorAll(".teleport-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const [x, y, z] = btn.dataset.location.split(",").map(Number);
            viewerCamera.position.set(x, y, z);
            if (typeof updateCoordinates === "function") updateCoordinates();
            document.getElementById("sidePanel").classList.remove("open");
        });
    });
    </script>';
}
?>