<?php
function setupControls() {
    echo '<script>
    // Movement controls using global camera
    window.viewerControls = {
        keys: { W: false, A: false, S: false, D: false },
        speed: 0.1,
        targetRotationY: 0,
        targetRotationX: 0, // Add pitch
        isMouseDown: false,
        mouseX: 0,
        mouseY: 0
    };

    // Event listeners
    window.addEventListener("keydown", (e) => {
        if (e.key.toUpperCase() in viewerControls.keys) {
            viewerControls.keys[e.key.toUpperCase()] = true;
        }
    });

    window.addEventListener("keyup", (e) => {
        if (e.key.toUpperCase() in viewerControls.keys) {
            viewerControls.keys[e.key.toUpperCase()] = false;
        }
    });

    // Mouse controls
    window.addEventListener("mousedown", (e) => {
        viewerControls.isMouseDown = true;
        viewerControls.mouseX = e.clientX;
        viewerControls.mouseY = e.clientY;
    });

    window.addEventListener("mouseup", () => {
        viewerControls.isMouseDown = false;
    });

    window.addEventListener("mousemove", (e) => {
        if (viewerControls.isMouseDown) {
            const deltaX = e.clientX - viewerControls.mouseX;
            const deltaY = e.clientY - viewerControls.mouseY;
            viewerControls.targetRotationY -= deltaX * 0.002;
            viewerControls.targetRotationX -= deltaY * 0.002;
            // Clamp pitch to avoid flipping
            viewerControls.targetRotationX = Math.max(-Math.PI/2 + 0.1, Math.min(Math.PI/2 - 0.1, viewerControls.targetRotationX));
            viewerControls.mouseX = e.clientX;
            viewerControls.mouseY = e.clientY;
        }
    });
    </script>';
}

function animationLoop() {
    echo '<script>
    function animate(timestamp) {
        requestAnimationFrame(animate);

        // Update camera rotation (no roll, keep camera upright)
        if (typeof viewerCamera !== "undefined") {
            viewerCamera.rotation.order = "YXZ"; // Yaw (y), Pitch (x), Roll (z)
            viewerCamera.rotation.y = viewerControls.targetRotationY; // yaw (left/right)
            viewerCamera.rotation.x = viewerControls.targetRotationX; // pitch (up/down)
            viewerCamera.rotation.z = 0; // always keep upright, no roll

            // Movement calculation
            const forward = new THREE.Vector3();
            viewerCamera.getWorldDirection(forward);
            forward.y = 0;
            forward.normalize();

            const right = new THREE.Vector3();
            right.crossVectors(forward, viewerCamera.up).normalize();

            // Apply movement
            if (viewerControls.keys.W) viewerCamera.position.add(forward.clone().multiplyScalar(viewerControls.speed));
            if (viewerControls.keys.S) viewerCamera.position.add(forward.clone().multiplyScalar(-viewerControls.speed));
            if (viewerControls.keys.A) viewerCamera.position.add(right.clone().multiplyScalar(-viewerControls.speed));
            if (viewerControls.keys.D) viewerCamera.position.add(right.clone().multiplyScalar(viewerControls.speed));

            if (typeof updateCoordinates === "function") updateCoordinates();
            
            if (typeof viewerRenderer !== "undefined" && typeof viewerScene !== "undefined") {
                viewerRenderer.render(viewerScene, viewerCamera);
            }
        }
    }
    requestAnimationFrame(animate);
    </script>';
}
?>