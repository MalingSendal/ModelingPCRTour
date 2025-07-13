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
    <title>Masjid Madinatul Ilm</title>
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
    
    <script src="includes/collision.js"></script>
</body>
</html>