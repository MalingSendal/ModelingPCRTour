<?php
// config.php
define('MODEL_PATH', 'assets/school.glb');  // Define the model path
define('THREEJS_VERSION', 'r128');          // Three.js version

// Set Content Security Policy header
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
?>