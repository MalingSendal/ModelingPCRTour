<?php
function setupTeleport() {
    echo '<script>
    document.querySelectorAll(".teleport-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const [x, y, z] = btn.dataset.location.split(",").map(Number);
            viewerCamera.position.set(x, y, z);
            
            // Adjust camera height to be 2 units above floor, with max height difference of 1
            const raycaster = new THREE.Raycaster();
            raycaster.set(new THREE.Vector3(x, y + 10, z), new THREE.Vector3(0, -1, 0));
            const intersects = raycaster.intersectObjects(viewerScene.children, true);
            
            let closestValidY = null;
            for (const intersect of intersects) {
                if (intersect.object.isMesh && !intersect.object.userData.noCollision) {
                    const floorY = intersect.point.y;
                    if (Math.abs(floorY - y) <= 1) {
                        if (closestValidY === null || floorY > closestValidY) {
                            closestValidY = floorY;
                        }
                    }
                }
            }
            
            if (closestValidY !== null) {
                viewerCamera.position.y = closestValidY + 2;
            } else {
                console.warn("No valid floor detected at teleport location within 1 unit, using default y:", y);
                viewerCamera.position.y = y;
            }
            
            if (typeof updateCoordinates === "function") updateCoordinates();
            document.getElementById("sidePanel").classList.remove("open");
        });
    });
    </script>';
}
?>