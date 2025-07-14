// COLLISION SYSTEM
const collisionSystem = {
    objects: [],
    camera: {
        radius: 0.2,
        offset: new THREE.Vector3(0, -2.2, 0)
    },
    lastSafePositions: [],
    debug: false
};

// Get floor height at position
function getFloorHeight(position) {
    const raycaster = new THREE.Raycaster();
    raycaster.set(new THREE.Vector3(position.x, position.y + 10, position.z), new THREE.Vector3(0, -1, 0));
    const intersects = raycaster.intersectObjects(viewerScene.children, true);
    
    for (const intersect of intersects) {
        if (intersect.object.isMesh && !intersect.object.userData.noCollision) {
            return intersect.point.y;
        }
    }
    return null;
}

// Initialize collision
function initCollision() {
    collisionSystem.objects = [];
    viewerScene.traverse(function(obj) {
        if (obj.isMesh && obj.visible && !obj.userData.noCollision) {
            if (!obj.geometry.boundingBox) obj.geometry.computeBoundingBox();
            collisionSystem.objects.push(obj);
        }
    });
    
    const floorY = getFloorHeight(viewerCamera.position);
    if (floorY !== null) {
        viewerCamera.position.y = floorY + 2;
    }
    
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
        const box = obj.geometry.boundingBox.clone();
        box.applyMatrix4(obj.matrixWorld);
        if (box.intersectsSphere(cameraSphere)) {
            return true;
        }
    }
    
    return false;
}

// Move with collision detection
function moveWithCollision(direction, distance) {
    const originalPos = viewerCamera.position.clone();
    const step = distance / 3;
    
    for (let i = 0; i < 3; i++) {
        viewerCamera.position.add(direction.clone().multiplyScalar(step));
        if (checkCollision()) {
            viewerCamera.position.copy(originalPos);
            return false;
        }
    }
    
    const floorY = getFloorHeight(viewerCamera.position);
    if (floorY !== null) {
        viewerCamera.position.y = floorY + 2;
        collisionSystem.lastSafePositions.push(viewerCamera.position.clone());
        if (collisionSystem.lastSafePositions.length > 5) {
            collisionSystem.lastSafePositions.shift();
        }
    } else {
        viewerCamera.position.copy(originalPos);
        return false;
    }
    return true;
}

// Enhanced animation loop with collision
function startCollisionAwareAnimation() {
    function animate() {
        requestAnimationFrame(animate);
        
        viewerCamera.rotation.order = "YXZ";
        viewerCamera.rotation.y = viewerControls.targetRotationY;
        viewerCamera.rotation.x = viewerControls.targetRotationX;
        viewerCamera.rotation.z = 0;

        const forward = new THREE.Vector3();
        viewerCamera.getWorldDirection(forward);
        forward.y = 0;
        forward.normalize();

        const right = new THREE.Vector3();
        right.crossVectors(forward, viewerCamera.up).normalize();

        if (viewerControls.keys.W) moveWithCollision(forward, viewerControls.speed);
        if (viewerControls.keys.S) moveWithCollision(forward, -viewerControls.speed);
        if (viewerControls.keys.A) moveWithCollision(right, -viewerControls.speed);
        if (viewerControls.keys.D) moveWithCollision(right, viewerControls.speed);

        if (typeof updateCoordinates === "function") updateCoordinates();
        viewerRenderer.render(viewerScene, viewerCamera);
    }
    
    animate();
}

// Initialize when scene is ready
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        initCollision();
        startCollisionAwareAnimation();
    }, 100);
});