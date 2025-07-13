<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration FIRST
require_once 'includes/config.php';

// Then load other components
require_once 'includes/scene.php';
require_once 'includes/ui.php';
require_once 'includes/controls.php';
require_once 'includes/teleport.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gedung Utama</title>
    <style>
        body { margin: 0; overflow: hidden; }
        canvas { display: block; }
        #coordinates {
            position: absolute;
            bottom: 10px;
            right: 10px;
            color: white;
            font-family: Arial, sans-serif;
            font-size: 14px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 8px;
            border-radius: 4px;
        }
        
        /* Side Panel Styles */
        #sidePanel {
            position: absolute;
            left: -250px;
            top: 0;
            width: 250px;
            height: 100%;
            background-color: rgba(30, 30, 30, 0.9);
            transition: left 0.3s ease;
            z-index: 100;
            padding: 20px;
            box-sizing: border-box;
            color: white;
            font-family: Arial, sans-serif;
        }
        
        #sidePanel.open {
            left: 0;
        }
        
        #menuToggle {
            position: absolute;
            left: 10px;
            bottom: 10px;
            width: 50px;
            height: 50px;
            background-color: rgba(30, 30, 30, 0.9);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 101;
            color: white;
            font-size: 24px;
            user-select: none;
        }
        
        .teleport-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #444;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .teleport-btn:hover {
            background-color: #666;
        }
        
        h2 {
            color: #fff;
            border-bottom: 1px solid #555;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Load Three.js first -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/loaders/GLTFLoader.js"></script>

    <?php
    // Initialize components
    setupScene();
    setupUI();
    setupControls();
    setupTeleport();
    ?>
    
    <!-- Collision System Implementation -->
    <script>
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
    </script>
</body>
</html>