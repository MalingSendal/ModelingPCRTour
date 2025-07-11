<?php
function setupCollision() {
    echo '<script>
    // Collision detection variables
    window.collidableObjects = [];
    window.lastSafePosition = new THREE.Vector3();
    window.collisionDistance = 0.5; // Distance to maintain from objects
    
    // Initialize collision system
    function initCollision() {
        // Store the initial safe position
        lastSafePosition.copy(camera.position);
        
        // Find all collidable objects in the scene
        scene.traverse(function(object) {
            if (object.isMesh) {
                collidableObjects.push(object);
            }
        });
    }
    
    // Check for collisions
    function checkCollisions() {
        // Create rays in multiple directions
        const directions = [
            new THREE.Vector3(1, 0, 0),   // Right
            new THREE.Vector3(-1, 0, 0),  // Left
            new THREE.Vector3(0, 0, 1),   // Forward
            new THREE.Vector3(0, 0, -1),  // Backward
            new THREE.Vector3(0, -1, 0)   // Down (for ground)
        ];
        
        let hitSomething = false;
        
        directions.forEach(dir => {
            const raycaster = new THREE.Raycaster(
                camera.position,
                dir,
                0,
                collisionDistance
            );
            
            const intersects = raycaster.intersectObjects(collidableObjects, true);
            
            if (intersects.length > 0) {
                hitSomething = true;
            }
        });
        
        if (hitSomething) {
            // Revert to last safe position if collision detected
            camera.position.copy(lastSafePosition);
        } else {
            // Update safe position if no collision
            lastSafePosition.copy(camera.position);
        }
    }
    
    // Initialize collision after model loads
    window.addEventListener("model-loaded", () => {
        initCollision();
    });
    </script>';
}
?>