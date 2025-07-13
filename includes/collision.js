// COLLISION SYSTEM
const collisionSystem = {
    objects: [],
    camera: {
        radius: 0.2, // Increased for better collision
        offset: new THREE.Vector3(0, -2.2, 0) // Adjusted for avatar height
    },
    lastSafePositions: [],
    debug: false
};

// Initialize collision
function initCollision() {
    // Clear previous objects
    collisionSystem.objects = [];
    
    // Find all collidable objects
    viewerScene.traverse(function(obj) {
        if (obj.isMesh && obj.visible && !obj.userData.noCollision) {
            // Compute bounding volumes if needed
            if (!obj.geometry.boundingBox) obj.geometry.computeBoundingBox();
            collisionSystem.objects.push(obj);
        }
    });
    
    // Store initial safe position
    collisionSystem.lastSafePositions = [viewerCamera.position.clone()];
    
    if (collisionSystem.debug) {
        console.log('Collision system ready with', collisionSystem.objects.length, 'objects');
    }
}

// Check for collisions
function checkCollision() {
    const cameraWorldPos = viewerCamera.position.clone().add(collisionSystem.camera.offset);
    const cameraSphere = new THREE.Sphere(cameraWorldPos, collisionSystem.camera.radius);
    
    for (const obj of collisionSystem.objects) {
        if (!obj.geometry.boundingBox) continue;
        
        // Get world-space bounding box
        const box = obj.geometry.boundingBox.clone();
        box.applyMatrix4(obj.matrixWorld);
        
        // Check collision
        if (box.intersectsSphere(cameraSphere)) {
            return true; // Collision detected
        }
    }
    
    // Store safe position (keep last 5 positions)
    collisionSystem.lastSafePositions.push(viewerCamera.position.clone());
    if (collisionSystem.lastSafePositions.length > 5) {
        collisionSystem.lastSafePositions.shift();
    }
    
    return false;
}

// Move with collision detection
function moveWithCollision(direction, distance) {
    const originalPos = viewerCamera.position.clone();
    const step = distance / 3; // Smaller steps for better detection
    
    // Try moving in increments
    for (let i = 0; i < 3; i++) {
        viewerCamera.position.add(direction.clone().multiplyScalar(step));
        if (checkCollision()) {
            // Revert to last safe position if collision
            viewerCamera.position.copy(originalPos);
            return false;
        }
    }
    return true;
}

// Enhanced animation loop with collision
function startCollisionAwareAnimation() {
    function animate() {
        requestAnimationFrame(animate);
        
        // Handle rotation
        viewerCamera.rotation.order = "YXZ";
        viewerCamera.rotation.y = viewerControls.targetRotationY;
        viewerCamera.rotation.x = viewerControls.targetRotationX;
        viewerCamera.rotation.z = 0;

        // Movement vectors
        const forward = new THREE.Vector3();
        viewerCamera.getWorldDirection(forward);
        forward.y = 0;
        forward.normalize();

        const right = new THREE.Vector3();
        right.crossVectors(forward, viewerCamera.up).normalize();

        // Apply collision-aware movement
        if (viewerControls.keys.W) moveWithCollision(forward, viewerControls.speed);
        if (viewerControls.keys.S) moveWithCollision(forward, -viewerControls.speed);
        if (viewerControls.keys.A) moveWithCollision(right, -viewerControls.speed);
        if (viewerControls.keys.D) moveWithCollision(right, viewerControls.speed);

        // Update UI and render
        if (typeof updateCoordinates === "function") updateCoordinates();
        viewerRenderer.render(viewerScene, viewerCamera);
    }
    
    animate();
}

// Initialize everything when scene is ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait a frame to ensure all components are loaded
    setTimeout(function() {
        initCollision();
        startCollisionAwareAnimation();
    }, 100);
});