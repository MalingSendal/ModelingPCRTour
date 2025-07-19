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
        <p>Approach glowing spheres to teleport.</p>
    </div>
    <button id="menuButton">☰</button>
    <div id="sidePanel">
        <button id="mainMenuButton">Back to Main Menu</button>
        <h3 onclick="toggleMenu('buildingA')">Building A</h3>
        <div id="buildingA" class="submenu">
            <button onclick="teleportTo(10, 1.6, 10)">Class</button>
            <button onclick="teleportTo(15, 1.6, 15)">Lab</button>
            <button onclick="teleportTo(20, 1.6, 20)">Library</button>
        </div>
        <h3 onclick="toggleMenu('buildingB')">Building B</h3>
        <div id="buildingB" class="submenu">
            <button onclick="teleportTo(30, 1.6, 30)">Class</button>
            <button onclick="teleportTo(35, 1.6, 35)">Lab</button>
            <button onclick="teleportTo(40, 1.6, 40)">Auditorium</button>
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
        renderer.setClearColor(0xd3d3d3, 1); // Set background to light gray
        document.body.appendChild(renderer.domElement);

        // Lighting setup
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.3);
        directionalLight.position.set(50, 50, 50);
        directionalLight.target.position.set(0, 0, 0);
        scene.add(directionalLight);
        scene.add(directionalLight.target);

        // First-person controls
        const controls = new THREE.PointerLockControls(camera, document.body);
        scene.add(controls.getObject());

        // Disable default pointer lock behavior
        controls.lock = function() {};
        controls.unlock = function() {};

        // Camera initial position
        camera.position.set(0, 1.4, 0);

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

                yaw -= deltaX * sensitivity;
                pitch -= deltaY * sensitivity;
                pitch = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, pitch));

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

        // Teleport point setup
        const teleportPoints = [
            {
                //Lantai Satu to Wing Kiri
                position: new THREE.Vector3(-26.58, 3.37, -45.45), // Teleport point location
                destination: new THREE.Vector3(-27.81, 3.99, -33.99), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Wing Kiri to Lantai Satu
                position: new THREE.Vector3(-25.02, 4.04, -34.25), // Teleport point location
                destination: new THREE.Vector3(-24.18, 3.38, -45.40), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Lantai Dua to Wing Kiri
                position: new THREE.Vector3(-27.09, 7.58, -44.43), // Teleport point location
                destination: new THREE.Vector3(-23.95, 8.50, -32.35), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Wing Kiri to Lantai Dua
                position: new THREE.Vector3(-24.44, 8.50, -36.11), // Teleport point location
                destination: new THREE.Vector3(-24.91, 7.58, -46.85), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Lantai Tiga to Wing Kiri
                position: new THREE.Vector3(-27.09, 11.90, -44.43), // Teleport point location
                destination: new THREE.Vector3(-23.95, 12.76, -32.35), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Wing Kiri to Lantai Tiga
                position: new THREE.Vector3(-24.44, 12.76, -36.11), // Teleport point location
                destination: new THREE.Vector3(-24.91, 11.90, -46.85), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI Test
                position: new THREE.Vector3(-6.60, 5.56, -32.97), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.2 #1
                position: new THREE.Vector3(-29.98, 9.33, -31.22), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.2 #2
                position: new THREE.Vector3(-40.25, 9.35, -31.46), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.2 #3
                position: new THREE.Vector3(-43, 9.33, -43.81), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.2 #4
                position: new THREE.Vector3(-43, 9.32, -54.38), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.2 #5
                position: new THREE.Vector3(-43, 9.31, -64.63), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.2 #6
                position: new THREE.Vector3(-43, 9.30, -75.21), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.2 #7
                position: new THREE.Vector3(-43, 9.30, -86.04), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.3 #1
                position: new THREE.Vector3(-29.88, 13.18, -31.19), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.3 #2
                position: new THREE.Vector3(-40.57, 13.19, -31.20), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.3 #3
                position: new THREE.Vector3(-42.55, 13.18, -43.84), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.3 #4
                position: new THREE.Vector3(-42.55, 13.17, -54.36), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.3 #5
                position: new THREE.Vector3(-42.55, 13.16, -64.81), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.3 #6
                position: new THREE.Vector3(-42.55, 13.15, -75.30), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTI LT.3 #7
                position: new THREE.Vector3(-42.55, 13.14, -86.11), // Teleport point location
                destination: new THREE.Vector3(-210.29, 2.90, -90.90), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Exit Labor JTI
                position: new THREE.Vector3(-213.78, 2.90, -90.75), // Teleport point location
                destination: new THREE.Vector3(-36.93, 9.35, -32.07), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Perpustakaan
                position: new THREE.Vector3(-6.22, 7.74, -52.38), // Teleport point location
                destination: new THREE.Vector3(-260.74, 2.59, -94.50), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Keluar Perpustakaan
                position: new THREE.Vector3(-265.78, 2.56, -89.56), // Teleport point location
                destination: new THREE.Vector3(-1.86, 7.74, -51.11), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Kelas #1
                position: new THREE.Vector3(-32.55, 4.69, -33.35), // Teleport point location
                destination: new THREE.Vector3(-204, 3.25, -97.04), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Kelas #2
                position: new THREE.Vector3(-40.00, 4.69, -39.38), // Teleport point location
                destination: new THREE.Vector3(-204, 3.25, -97.04), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Kelas #3
                position: new THREE.Vector3(-39.68, 4.68, -50.64), // Teleport point location
                destination: new THREE.Vector3(-204, 3.25, -97.04), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Kelas #4
                position: new THREE.Vector3(-39.74, 4.68, -61.92), // Teleport point location
                destination: new THREE.Vector3(-204, 3.25, -97.04), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Kelas #5
                position: new THREE.Vector3(-39.94, 4.67, -73.10), // Teleport point location
                destination: new THREE.Vector3(-204, 3.25, -97.04), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Kelas #6
                position: new THREE.Vector3(-40.02, 4.66, -84.42), // Teleport point location
                destination: new THREE.Vector3(-204, 3.25, -97.04), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTIN #1
                position: new THREE.Vector3(-36.56, 4.65, -89.84), // Teleport point location
                destination: new THREE.Vector3(-322.76, 3.23, -99), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //To Labor JTIN #2
                position: new THREE.Vector3(-25.36, 4.67, -89.86), // Teleport point location
                destination: new THREE.Vector3(-322.76, 3.23, -99), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Keluar Labor JTIN
                position: new THREE.Vector3(-321.72, 2.91, -90.67), // Teleport point location
                destination: new THREE.Vector3(-31, 4.66, -89.79), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            },
            {
                //Keluar Kelas
                position: new THREE.Vector3(-203.39, 2.7, -91.16), // Teleport point location
                destination: new THREE.Vector3(-36, 3.95, -34), // Destination
                radius: 1.0, // Trigger distance
                sphere: null // Will hold the visible sphere mesh
            }
        ];

        // Create visible teleport point
        teleportPoints.forEach(point => {
            const geometry = new THREE.SphereGeometry(0.3, 32, 32);
            const material = new THREE.MeshBasicMaterial({ color: 0x00ff00, transparent: true, opacity: 0.7 });
            point.sphere = new THREE.Mesh(geometry, material);
            point.sphere.position.copy(point.position);
            scene.add(point.sphere);

            // Add subtle pulsing animation
            point.sphere.scale.set(1, 1, 1);
            point.pulsePhase = Math.random() * Math.PI * 2;
        });

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

            // Update raycaster origin
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

            // Check for teleport points
            teleportPoints.forEach(point => {
                // Pulse animation
                point.pulsePhase += 0.05;
                const scale = 1 + 0.1 * Math.sin(point.pulsePhase);
                point.sphere.scale.set(scale, scale, scale);

                // Check distance to teleport point
                const distance = camera.position.distanceTo(point.position);
                if (distance < point.radius) {
                    teleportTo(point.destination.x, point.destination.y, point.destination.z);
                }
            });

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