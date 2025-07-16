<?php
// Set the content type to HTML
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School 3D Virtual Tour</title>
    <style>
        body { margin: 0; overflow: hidden; }
        canvas { display: block; }
        #instructions {
            position: absolute;
            top: 10px;
            left: 10px;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div id="instructions">
        <p>Use WASD to move, mouse to look around.</p>
        <p>Click to lock the mouse cursor.</p>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/PointerLockControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script>
        // Scene setup
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5); // Soft ambient light
        scene.add(ambientLight);
        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8); // Directional light for shadows
        directionalLight.position.set(10, 20, 15);
        scene.add(directionalLight);

        // First-person controls
        const controls = new THREE.PointerLockControls(camera, document.body);
        document.addEventListener('click', () => controls.lock());
        scene.add(controls.getObject());

        // Camera initial position
        camera.position.set(0, 400, 0); // Eye-level height (1.6m)

        // Movement variables
        const moveSpeed = 0.1;
        const velocity = new THREE.Vector3();
        let moveForward = false, moveBackward = false, moveLeft = false, moveRight = false;

        // Keyboard controls
        document.addEventListener('keydown', (event) => {
            switch (event.code) {
                case 'KeyW': moveForward = true; break;
                case 'KeyS': moveBackward = true; break;
                case 'KeyA': moveLeft = true; break;
                case 'KeyD': moveRight = true; break;
            }
        });
        document.addEventListener('keyup', (event) => {
            switch (event.code) {
                case 'KeyW': moveForward = false; break;
                case 'KeyS': moveBackward = false; break;
                case 'KeyA': moveLeft = false; break;
                case 'KeyD': moveRight = false; break;
            }
        });

        // Load GLB model
        const loader = new THREE.GLTFLoader();
        loader.load(
            'assets/tmptpcr.glb', // Path to your GLB model
            (gltf) => {
                scene.add(gltf.scene);
                console.log('Model loaded successfully');
            },
            (xhr) => {
                console.log((xhr.loaded / xhr.total * 100) + '% loaded');
            },
            (error) => {
                console.error('Error loading model:', error);
            }
        );

        // Raycaster for floor detection
        const raycaster = new THREE.Raycaster();
        raycaster.ray.direction.set(0, -1, 0); // Downward ray
        const gravity = 0.1; // Gravity for smooth falling

        // Basic collision detection rays (forward, backward, left, right)
        const collisionRays = [
            new THREE.Raycaster(), // Forward
            new THREE.Raycaster(), // Backward
            new THREE.Raycaster(), // Left
            new THREE.Raycaster(), // Right
        ];
        const rayDirections = [
            new THREE.Vector3(0, 0, -1), // Forward
            new THREE.Vector3(0, 0, 1),  // Backward
            new THREE.Vector3(-1, 0, 0), // Left
            new THREE.Vector3(1, 0, 0),  // Right
        ];

        // Animation loop
        function animate() {
            requestAnimationFrame(animate);

            // Update raycaster origin
            raycaster.ray.origin.copy(camera.position);

            // Check for floor height
            const intersects = raycaster.intersectObjects(scene.children, true);
            let floorHeight = -100; // Default low value if no floor
            for (let intersect of intersects) {
                if (intersect.object.name.includes('floor')) {
                    floorHeight = intersect.point.y;
                    break;
                }
            }

            // Adjust camera height (snap to floor + eye height)
            if (floorHeight > -100) {
                camera.position.y = floorHeight + 1.6; // Eye-level height
            } else {
                velocity.y -= gravity; // Apply gravity if no floor
            }

            // Apply velocity to camera position
            camera.position.y += velocity.y;

            // Update movement
            velocity.x = 0;
            velocity.z = 0;
            const direction = new THREE.Vector3();
            if (moveForward) direction.z -= 1;
            if (moveBackward) direction.z += 1;
            if (moveLeft) direction.x -= 1;
            if (moveRight) direction.x += 1;

            // Normalize direction and apply speed
            direction.normalize().multiplyScalar(moveSpeed);
            direction.applyQuaternion(controls.getObject().quaternion);

            // Collision detection
            let canMove = true;
            for (let i = 0; i < collisionRays.length; i++) {
                collisionRays[i].ray.origin.copy(camera.position);
                collisionRays[i].ray.direction.copy(rayDirections[i]).applyQuaternion(controls.getObject().quaternion);
                const collisions = collisionRays[i].intersectObjects(scene.children, true);
                for (let collision of collisions) {
                    if (collision.distance < 0.5) { // Adjust distance threshold as needed
                        canMove = false;
                        break;
                    }
                }
                if (!canMove) break;
            }

            if (canMove) {
                velocity.x = direction.x;
                velocity.z = direction.z;
            } else {
                velocity.x = 0;
                velocity.z = 0;
            }

            // Update camera position
            controls.getObject().position.add(velocity);

            renderer.render(scene, camera);
        }
        animate();

        // Handle window resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>