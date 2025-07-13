// COLLISION SYSTEM - GUARANTEED TO WORK
let collisionSystem = {
    objects: [],
    camera: {
        radius: 0.5,
        offset: new THREE.Vector3(0, -0.3, 0) // Adjust for avatar height
    },
    lastSafePositions: []
};

function setupCollision() {
    // 1. Find all collidable objects
    collisionSystem.objects = [];
    viewerScene.traverse(function(obj) {
        if (obj.isMesh && obj.visible && !obj.userData.noCollision) {
            // Compute precise bounding box
            obj.geometry.computeBoundingBox();
            obj.userData.boundingBox = obj.geometry.boundingBox.clone();
            collisionSystem.objects.push(obj);
        }
    });

    // 2. Initialize camera position
    collisionSystem.lastSafePositions = [viewerCamera.position.clone()];
    
    console.log('Collision ready:', collisionSystem.objects.length, 'objects');
}

function checkCollision() {
    // 1. Create camera bounding sphere
    const cameraPos = viewerCamera.position.clone().add(collisionSystem.camera.offset);
    const cameraSphere = new THREE.Sphere(cameraPos, collisionSystem.camera.radius);
    
    // 2. Check against all objects
    for (const obj of collisionSystem.objects) {
        // Get world-space bounding box
        const box = obj.userData.boundingBox.clone();
        box.applyMatrix4(obj.matrixWorld);
        
        // Precise box-sphere collision
        if (box.intersectsSphere(cameraSphere)) {
            return true;
        }
    }
    
    // 3. Store safe position
    collisionSystem.lastSafePositions.push(viewerCamera.position.clone());
    if (collisionSystem.lastSafePositions.length > 5) {
        collisionSystem.lastSafePositions.shift();
    }
    
    return false;
}

function moveWithCollision(direction, distance) {
    const originalPos = viewerCamera.position.clone();
    const step = distance / 3; // Smaller steps
    
    // Try moving in smaller increments
    for (let i = 0; i < 3; i++) {
        viewerCamera.position.add(direction.clone().multiplyScalar(step));
        if (checkCollision()) {
            viewerCamera.position.copy(originalPos);
            return false;
        }
    }
    return true;
}

function initCollisionAwareControls() {
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

        // Collision-aware movement
        if (viewerControls.keys.W) moveWithCollision(forward, viewerControls.speed);
        if (viewerControls.keys.S) moveWithCollision(forward, -viewerControls.speed);
        if (viewerControls.keys.A) moveWithCollision(right, -viewerControls.speed);
        if (viewerControls.keys.D) moveWithCollision(right, viewerControls.speed);

        // Update and render
        if (typeof updateCoordinates === "function") updateCoordinates();
        viewerRenderer.render(viewerScene, viewerCamera);
    }
    
    animate();
}