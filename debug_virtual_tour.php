<?php
// Set the content type to HTML
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School 3D Virtual Tour - Debug FreeCam</title>
    <style>
        body { margin: 0; overflow: hidden; }
        canvas { display: block; }
        #instructions {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            font-family: Arial, sans-serif;
        }
        #coordinates {
            position: absolute;
            bottom: 10px;
            right: 10px;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div id="instructions">
        <p>Use WASD to move, Space to ascend, Shift to descend.</p>
        <p>Hold left-click and drag to look around.</p>
    </div>
    <div id="coordinates">X: 0, Y: 0, Z: 0</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/PointerLockControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script>
        // Scene setup
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setClearColor(0xd3d3d3, 1); // Set background to light gray
        document.body.appendChild(renderer.domElement);

        // Lighting setup
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6); // Soft white light, low intensity
        scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.4); // Gentle directional light
        directionalLight.position.set(50, 50, 50); // Positioned high and far to cover wide area
        directionalLight.target.position.set(0, 0, 0); // Pointing towards scene center
        scene.add(directionalLight);
        scene.add(directionalLight.target);

        // First-person controls
        const controls = new THREE.PointerLockControls(camera, document.body);
        scene.add(controls.getObject());

        // Disable default pointer lock behavior
        controls.lock = function() {};
        controls.unlock = function() {};

        // Camera initial position
        camera.position.set(0, 1.6, 0);

        // Movement variables
        const moveSpeed = 0.2; // Increased speed for debug
        const velocity = new THREE.Vector3();
        let moveForward = false, moveBackward = false, moveLeft = false, moveRight = false, moveUp = false, moveDown = false;

        // Mouse control variables
        let isMouseDown = false;
        let prevMouseX = 0;
        let prevMouseY = 0;
        const sensitivity = 0.002;
        let yaw = 0;
        let pitch = 0;

        // Mouse events for drag-to-look
        document.addEventListener('mousedown', (event) => {
            if (event.button === 0) {
                isMouseDown = true;
                prevMouseX = event.clientX;
                prevMouseY = event.clientY;
            }
        });
        document.addEventListener('mouseup', (event) => {
            if (event.button === 0) {
                isMouseDown = false;
            }
        });
        document.addEventListener('mousemove', (event) => {
            if (isMouseDown) {
                const deltaX = event.clientX - prevMouseX;
                const deltaY = event.clientY - prevMouseY;
                prevMouseX = event.clientX;
                prevMouseY = event.clientY;

                // Update yaw and pitch
                yaw -= deltaX * sensitivity;
                pitch -= deltaY * sensitivity;
                pitch = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, pitch));

                // Apply rotation to camera using quaternion
                const euler = new THREE.Euler(pitch, yaw, 0, 'YXZ');
                controls.getObject().quaternion.setFromEuler(euler);
            }
        });

        // Keyboard controls
        document.addEventListener('keydown', (event) => {
            switch (event.code) {
                case 'KeyW': moveForward = true; break;
                case 'KeyS': moveBackward = true; break;
                case 'KeyA': moveLeft = true; break;
                case 'KeyD': moveRight = true; break;
                case 'Space': moveUp = true; break;
                case 'ShiftLeft': case 'ShiftRight': moveDown = true; break;
            }
        });
        document.addEventListener('keyup', (event) => {
            switch (event.code) {
                case 'KeyW': moveForward = false; break;
                case 'KeyS': moveBackward = false; break;
                case 'KeyA': moveLeft = false; break;
                case 'KeyD': moveRight = false; break;
                case 'Space': moveUp = false; break;
                case 'ShiftLeft': case 'ShiftRight': moveDown = false; break;
            }
        });

        // Load GLB model
        const loader = new THREE.GLTFLoader();
        loader.load(
            'assets/tmptpcr.glb',
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

        // Coordinate display
        const coordDisplay = document.getElementById('coordinates');

        // Animation loop
        function animate() {
            requestAnimationFrame(animate);

            // Update movement
            velocity.set(0, 0, 0);
            const direction = new THREE.Vector3();
            if (moveForward) direction.z -= 1;
            if (moveBackward) direction.z += 1;
            if (moveLeft) direction.x -= 1;
            if (moveRight) direction.x += 1;
            if (moveUp) direction.y += 1;
            if (moveDown) direction.y -= 1;

            // Normalize direction and apply speed
            direction.normalize().multiplyScalar(moveSpeed);
            direction.applyQuaternion(controls.getObject().quaternion);

            // Apply velocity to camera position (no collision or gravity)
            controls.getObject().position.add(direction);

            // Update coordinate display
            coordDisplay.textContent = `X: ${camera.position.x.toFixed(2)}, Y: ${camera.position.y.toFixed(2)}, Z: ${camera.position.z.toFixed(2)}`;

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