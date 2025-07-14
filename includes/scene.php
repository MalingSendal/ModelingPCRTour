<?php
function setupScene($cameraX = 0, $cameraY = 2, $cameraZ = 5) {
    echo '<script>
    // Create and expose Three.js objects
    window.viewerScene = new THREE.Scene();
    window.viewerCamera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
    window.viewerRenderer = new THREE.WebGLRenderer({ antialias: true });
    
    viewerRenderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(viewerRenderer.domElement);

    // Lighting
    // HemisphereLight for soft, even lighting
    const hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x888888, 0.1); // Increased intensity and brighter ground color
    hemisphereLight.position.set(0, 20, 0);
    viewerScene.add(hemisphereLight);

    // DirectionalLight for strong, directional lighting (like sunlight)
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8); // White light, moderate intensity
    directionalLight.position.set(10, 20, 10); // Positioned above and to the side
    directionalLight.castShadow = true; // Enable shadows (optional)
    directionalLight.shadow.mapSize.width = 2048; // Higher resolution shadows
    directionalLight.shadow.mapSize.height = 2048;
    directionalLight.shadow.camera.near = 0.5;
    directionalLight.shadow.camera.far = 50;
    viewerScene.add(directionalLight);

    // AmbientLight to reduce dark spots
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.3); // Soft ambient light
    viewerScene.add(ambientLight);

    // Model loading
    const loader = new THREE.GLTFLoader();
    loader.load(
        "'.MODEL_PATH.'",
        (gltf) => {
            viewerScene.add(gltf.scene);
            gltf.scene.position.set(0, 0, 0);
            gltf.scene.scale.set(1, 1, 1);
            
            // Adjust initial camera height
            const raycaster = new THREE.Raycaster();
            raycaster.set(new THREE.Vector3('.$cameraX.', '.$cameraY.' + 10, '.$cameraZ.'), new THREE.Vector3(0, -1, 0));
            const intersects = raycaster.intersectObjects(gltf.scene.children, true);
            
            let floorY = null;
            for (const intersect of intersects) {
                if (intersect.object.isMesh && !intersect.object.userData.noCollision) {
                    floorY = intersect.point.y;
                    break;
                }
            }
            
            if (floorY !== null) {
                viewerCamera.position.set('.$cameraX.', floorY + 2, '.$cameraZ.');
            } else {
                console.warn("No floor detected at initial camera position, using default y: '.$cameraY.'");
                viewerCamera.position.set('.$cameraX.', '.$cameraY.', '.$cameraZ.');
            }
            
            window.dispatchEvent(new CustomEvent("model-loaded"));
        },
        (xhr) => console.log((xhr.loaded/xhr.total*100) + "% loaded"),
        (error) => console.error("Error loading model:", error)
    );
    </script>';
}
?>