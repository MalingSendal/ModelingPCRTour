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
            right: 10px;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            font-family: Arial, sans-serif;
        }
        #menuButton {
            position: absolute;
            bottom: 10px;
            left: 10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            pointer-events: auto;
        }
        #sidePanel {
            position: absolute;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 20px;
            transition: left 0.3s ease;
            font-family: Arial, sans-serif;
            z-index: 999;
        }
        #sidePanel.open {
            left: 0;
        }
        #sidePanel button {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
            text-align: left;
        }
        #sidePanel button:hover {
            background: #555;
        }
        #sidePanel .submenu {
            margin-left: 20px;
            display: none;
        }
        #sidePanel .submenu.open {
            display: block;
        }
        #sidePanel h3 {
            cursor: pointer;
            margin: 10px 0;
        }
        /* START COORDINATE DISPLAY CSS - Remove this block to disable coordinate display */
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
        /* END COORDINATE DISPLAY CSS */
    </style>
</head>
<body>
    <div id="instructions">
        <p>Use WASD to move, hold left-click and drag to look around.</p>
        <p>Click menu button to open navigation.</p>
    </div>
    <button id="menuButton">☰</button>
    <div id="sidePanel">
        <button id="mainMenuButton">Back to Main Menu</button>
        <h3 onclick="toggleMenu('buildingA')">Building A</h3>
        <div id="buildingA" class="submenu">
            <button onclick="teleportTo(10, 1.6, 10)">Class</button> <!-- Change coordinates (x, y, z) here for Building A Class -->
            <button onclick="teleportTo(15, 1.6, 15)">Lab</button> <!-- Change coordinates (x, y, z) here for Building A Lab -->
            <button onclick="teleportTo(20, 1.6, 20)">Library</button> <!-- Change coordinates (x, y, z) here for Building A Library -->
        </div>
        <h3 onclick="toggleMenu('buildingB')">Building B</h3>
        <div id="buildingB" class="submenu">
            <button onclick="teleportTo(30, 1.6, 30)">Class</button> <!-- Change coordinates (x, y, z) here for Building B Class -->
            <button onclick="teleportTo(35, 1.6, 35)">Lab</button> <!-- Change coordinates (x, y, z) here for Building B Lab -->
            <button onclick="teleportTo(40, 1.6, 40)">Auditorium</button> <!-- Change coordinates (x, y, z) here for Building B Auditorium -->
        </div>
    </div>
    <!-- START COORDINATE DISPLAY HTML - Remove this element to disable coordinate display -->
    <div id="coordinates">X: 0, Y: 0, Z: 0</div>
    <!-- END COORDINATE DISPLAY HTML -->
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
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);
        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(10, 20, 15);
        scene.add(directionalLight);

        // First-person controls
        const controls = new THREE.PointerLockControls(camera, document.body);
        scene.add(controls.getObject());

        // Disable default pointer lock behavior
        controls.lock = function() {};
        controls.unlock = function() {};

        // Camera initial position
        camera.position.set(0, 1.6, 0);

        // Movement variables
        const moveSpeed = 0.1;
        const velocity = new THREE.Vector3();
        let moveForward = false, moveBackward = false, moveLeft = false, moveRight = false;

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

        // Raycaster for floor detection
        const raycaster = new THREE.Raycaster();
        raycaster.ray.direction.set(0, -1, 0);
        const gravity = 0.1;

        // Basic collision detection rays (forward, backward, left, right)
        const collisionRays = [
            new THREE.Raycaster(),
            new THREE.Raycaster(),
            new THREE.Raycaster(),
            new THREE.Raycaster(),
        ];
        const rayDirections = [
            new THREE.Vector3(0, 0, -1),
            new THREE.Vector3(0, 0, 1),
            new THREE.Vector3(-1, 0, 0),
            new THREE.Vector3(1, 0, 0),
        ];

        // Menu toggle
        const menuButton = document.getElementById('menuButton');
        const sidePanel = document.getElementById('sidePanel');
        menuButton.addEventListener('click', () => {
            sidePanel.classList.toggle('open');
        });

        // Main menu button
        document.getElementById('mainMenuButton').addEventListener('click', () => {
            window.location.reload();
        });

        // Collapsible menu toggle
        function toggleMenu(id) {
            const submenu = document.getElementById(id);
            submenu.classList.toggle('open');
        }

        // Teleportation function
        function teleportTo(x, y, z) {
            camera.position.set(x, y, z);
            controls.getObject().position.set(x, y, z);
        }

        // START COORDINATE DISPLAY JS - Remove this block to disable coordinate display
        const coordDisplay = document.getElementById('coordinates');
        // END COORDINATE DISPLAY JS

        // Animation loop
        function animate() {
            requestAnimationFrame(animate);

            // Update raycaster origin (from slightly above camera to ensure detection)
            raycaster.ray.origin.copy(camera.position);
            raycaster.ray.origin.y += 0.1;

            // Check for floor height
            const intersects = raycaster.intersectObjects(scene.children, true);
            let floorHeight = null;
            for (let intersect of intersects) {
                if (intersect.point && intersect.distance < 10) {
                    floorHeight = intersect.point.y;
                    break;
                }
            }

            // Adjust camera height
            if (floorHeight !== null) {
                camera.position.y = floorHeight + 1.6;
                velocity.y = 0;
            } else {
                velocity.y -= gravity;
                if (camera.position.y < -10) {
                    camera.position.y = 1.6;
                    velocity.y = 0;
                }
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
            let collisionDirection = null;
            for (let i = 0; i < collisionRays.length; i++) {
                collisionRays[i].ray.origin.copy(camera.position);
                collisionRays[i].ray.direction.copy(rayDirections[i]).applyQuaternion(controls.getObject().quaternion);
                const collisions = collisionRays[i].intersectObjects(scene.children, true);
                for (let collision of collisions) {
                    if (collision.distance < 0.5) {
                        canMove = false;
                        collisionDirection = rayDirections[i];
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
                if (collisionDirection) {
                    const pushBack = collisionDirection.clone().negate().multiplyScalar(0.1);
                    controls.getObject().position.add(pushBack);
                    camera.position.add(pushBack);
                }
            }

            // Update camera position
            controls.getObject().position.add(velocity);

            // START COORDINATE DISPLAY UPDATE - Remove this block to disable coordinate display
            coordDisplay.textContent = `X: ${camera.position.x.toFixed(2)}, Y: ${camera.position.y.toFixed(2)}, Z: ${camera.position.z.toFixed(2)}`;
            // END COORDINATE DISPLAY UPDATE

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