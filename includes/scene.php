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
    const light = new THREE.HemisphereLight(0xffffff, 0x444444);
    light.position.set(0, 20, 0);
    viewerScene.add(light);

    // Model loading - THIS IS WHERE GLTFLoader SHOULD BE INITIALIZED
    const loader = new THREE.GLTFLoader();
    loader.load(
        "'.MODEL_PATH.'",
        (gltf) => {
            viewerScene.add(gltf.scene);
            gltf.scene.position.set(0, 0, 0);
            gltf.scene.scale.set(1, 1, 1);
            window.dispatchEvent(new CustomEvent("model-loaded"));
        },
        (xhr) => console.log((xhr.loaded/xhr.total*100) + "% loaded"),
        (error) => console.error("Error loading model:", error)
    );

    // Initial camera position
    viewerCamera.position.set('.$cameraX.', '.$cameraY.', '.$cameraZ.');
    </script>';
}
?>