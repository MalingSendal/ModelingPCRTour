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
        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background-color: #85a9f7;
            color: #004d61;
            font-family: Arial, sans-serif;
        }

        canvas {
            display: block;
            width: 100%;
            height: 100%;
        }

        #instructions {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 77, 97, 0.8);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        #menuButton {
            position: absolute;
            bottom: 15px;
            left: 15px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(0, 77, 97, 0.8);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease, background-color 0.3s;
            z-index: 1000;
            pointer-events: auto;
        }

        #menuButton:hover {
            background: rgba(0, 77, 97, 0.9);
            transform: scale(1.1);
        }

        #menuButton:active {
            transform: scale(0.95);
        }

        #sidePanel {
            position: absolute;
            top: 0;
            left: -270px;
            width: 270px;
            height: 100%;
            background: rgba(0, 77, 97, 0.95);
            color: white;
            padding: 25px;
            transition: left 0.3s ease;
            font-family: Arial, sans-serif;
            z-index: 999;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.3);
        }

        #sidePanel.open {
            left: 0;
        }

        #sidePanel button {
            display: block;
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: left;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
        }

        #sidePanel button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.02);
        }

        #sidePanel .submenu {
            margin-left: 25px;
            display: none;
        }

        #sidePanel .submenu.open {
            display: block;
        }

        #sidePanel h3 {
            cursor: pointer;
            margin: 15px 0;
            font-size: 18px;
            color: #ffffff;
            transition: color 0.3s;
        }

        #sidePanel h3:hover {
            color: #e0e0e0;
        }

        /* Coordinate Display */
        #coordinates {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(0, 77, 97, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        /* Info Popup Styles */
        #infoPopup {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #004d61;
            border-radius: 12px;
            padding: 25px;
            display: none;
            z-index: 1000;
            font-family: Arial, sans-serif;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease forwards;
        }

        #infoPopup img {
            width: 100%;
            height: auto;
            max-height: 320px;
            object-fit: contain;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        #infoPopup p {
            margin: 12px 0;
            color: #004d61;
            font-size: 16px;
            line-height: 1.5;
        }

        #closePopup {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4444;
            color: white;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            line-height: 28px;
            text-align: center;
            transition: background-color 0.3s;
        }

        #closePopup:hover {
            background: #cc0000;
        }

        /* Animations */
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }
        }
    </style>
</head>
<body>
    <div id="instructions">
        <p>Use WASD to move, hold left-click and drag to look around.</p>
        <p>Click menu button to open navigation.</p>
        <p>Approach glowing spheres to teleport or blue spheres for info.</p>
    </div>
    <button id="menuButton">☰</button>
    <div id="sidePanel">
        <button id="mainMenuButton">Back to Main Menu</button>
        <h3 onclick="toggleMenu('buildingA')">Gedung Utama</h3>
        <div id="buildingA" class="submenu">
            <button onclick="teleportTo(-204, 3.25, -97.04)">Kelas</button>
            <button onclick="teleportTo(-210.29, 2.90, -90.90)">Laboratorium</button>
            <button onclick="teleportTo(-260.74, 2.59, -94.50)">Perpustakaan</button>
        </div>
        <h3 onclick="toggleMenu('buildingB')">Gedung Serba Guna</h3>
        <div id="buildingB" class="submenu">
            <button onclick="teleportTo(-77.65, 3.00, -145.04)">Class</button>
            <button onclick="teleportTo(35, 1.6, 35)">Lab</button>
            <button onclick="teleportTo(40, 1.6, 40)">Auditorium</button>
        </div>
        <h3 onclick="teleportTo(118.88, 2.47, -80.25)" style="cursor:pointer; margin:10px 0;">Kantin</h3>
        <h3 onclick="teleportTo(74.12, 4.23, -243.78)" style="cursor:pointer; margin:10px 0;">Workshop</h3>
        <h3 onclick="teleportTo(-110.69, 3.85, -116.70)" style="cursor:pointer; margin:10px 0;">Sport Hall</h3>
        <h3 onclick="teleportTo(62.28, 2.64, -84.68)" style="cursor:pointer; margin:10px 0;">Masjid Madinatul Ilm'</h3>
        <h3 onclick="teleportTo(-2.93, 5.66, -0.83)" style="cursor:pointer; margin:10px 0;">Un-Stuck</h3>
    </div>
    <!-- START COORDINATE DISPLAY HTML -->
    <div id="coordinates">X: 0, Y: 0, Z: 0</div>
    <!-- END COORDINATE DISPLAY HTML -->
    <!-- Info Popup HTML -->
    <div id="infoPopup">
        <button id="closePopup">X</button>
        <img id="infoImage" src="" alt="Info Image">
        <p id="infoText"></p>
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
        camera.position.set(0, 6, 0);

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

        // Info points setup
        const infoPoints = [
            {
                position: new THREE.Vector3(-204, 3.25, -97.04), // Near Kelas
                image: 'info/kelas_info.jpg',
                text: 'Welcome to the Classroom! This is where students engage in daily learning activities.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(-260.74, 2.59, -94.50), // Near Perpustakaan
                image: 'info/library_info.jpg',
                text: 'The Library offers a vast collection of books and resources for students and faculty.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(118.88, 2.47, -80.25), // Near Kantin
                image: 'info/canteen_info.jpg',
                text: 'The Canteen is a popular spot for students to enjoy meals and socialize.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(-210.29, 2.87, -90.90), // Near LabJTI
                image: 'info/labJTI_info.jpg',
                text: 'The Laboratorium is equipped with advanced technology for practical learning.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(-322.96, 2.91, -98.99), // Near LabJTIN
                image: 'info/labJTIN_info.jpg',
                text: 'The Laboratorium is equipped with advanced technology for practical learning.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(74.12, 4.23, -243.78), // Near Workshop
                image: 'info/workshop_info.jpg',
                text: 'The Workshop provides hands-on experience in various technical fields.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(-110.69, 3.85, -116.70), // Near Sport Hall
                image: 'info/sport_hall_info.jpg',
                text: 'The Sport Hall is a multi-purpose facility for sports and events.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(62.28, 2.64, -84.68), // Near Masjid Madinatul Ilm'
                image: 'info/masjid_info.jpg',
                text: 'Masjid Madinatul Ilm\' is a place of worship and community gathering.',
                radius: 2.0,
                sphere: null,
                active: false
            },
            {
                position: new THREE.Vector3(-77.65, 2.91, -145.04), // Near GSG
                image: 'info/gsg_info.jpg',
                text: 'Gedung Serba Guna (GSG)\' is a multi-purpose building for various activities.',
                radius: 2.0,
                sphere: null,
                active: false
            }
        ];

        // Create visible teleport and info points
        teleportPoints.forEach(point => {
            const geometry = new THREE.SphereGeometry(0.3, 32, 32);
            const material = new THREE.MeshBasicMaterial({ color: 0x00ff00, transparent: true, opacity: 0.7 });
            point.sphere = new THREE.Mesh(geometry, material);
            point.sphere.position.copy(point.position);
            scene.add(point.sphere);
            point.sphere.scale.set(1, 1, 1);
            point.pulsePhase = Math.random() * Math.PI * 2;
        });

        infoPoints.forEach(point => {
            const geometry = new THREE.SphereGeometry(0.3, 32, 32);
            const material = new THREE.MeshBasicMaterial({ color: 0x0000ff, transparent: true, opacity: 0.7 });
            point.sphere = new THREE.Mesh(geometry, material);
            point.sphere.position.copy(point.position);
            scene.add(point.sphere);
            point.sphere.scale.set(1, 1, 1);
            point.pulsePhase = Math.random() * Math.PI * 2;
        });

        // Raycaster for floor detection
        const raycaster = new THREE.Raycaster();
        raycaster.ray.direction.set(0, -1, 0);
        const gravity = 0.1;

        // Basic collision detection rays
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
            window.location.href = "index.php";
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

        // Info popup handling
        const infoPopup = document.getElementById('infoPopup');
        const infoImage = document.getElementById('infoImage');
        const infoText = document.getElementById('infoText');
        const closePopup = document.getElementById('closePopup');

        function showInfoPopup(imageSrc, text) {
            infoImage.src = imageSrc;
            infoText.textContent = text;
            infoPopup.style.display = 'block';
        }

        function hideInfoPopup() {
            infoPopup.style.display = 'none';
            infoPoints.forEach(point => point.active = false);
        }

        closePopup.addEventListener('click', hideInfoPopup);

        // Coordinate display
        const coordDisplay = document.getElementById('coordinates');

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

            // Check teleport points
            teleportPoints.forEach(point => {
                point.pulsePhase += 0.05;
                const scale = 1 + 0.1 * Math.sin(point.pulsePhase);
                point.sphere.scale.set(scale, scale, scale);

                const distance = camera.position.distanceTo(point.position);
                if (distance < point.radius) {
                    teleportTo(point.destination.x, point.destination.y, point.destination.z);
                }
            });

            // Check info points
            infoPoints.forEach(point => {
                point.pulsePhase += 0.05;
                const scale = 1 + 0.1 * Math.sin(point.pulsePhase);
                point.sphere.scale.set(scale, scale, scale);

                const distance = camera.position.distanceTo(point.position);
                if (distance < point.radius && !point.active) {
                    point.active = true;
                    showInfoPopup(point.image, point.text);
                } else if (distance >= point.radius && point.active) {
                    point.active = false;
                    hideInfoPopup();
                }
            });

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