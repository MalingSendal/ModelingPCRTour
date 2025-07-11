<?php
// halaman.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Virtual Tour Kampus</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #85a9f7;
      color: #004d61;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
      position: relative;
    }

    img.logo {
      max-width: 80%;
      height: auto;
      max-height: 20vh;
      margin-bottom: 20px;
    }

    h2 {
      font-size: 2em;
      margin: 0 0 10px;
    }

    .buttons {
      margin-top: 40px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .btn {
      background-color: rgba(0, 0, 0, 0.1);
      border: none;
      border-radius: 30px;
      padding: 15px 40px;
      font-size: 1em;
      color: white;
      cursor: pointer;
      transition: transform 0.3s ease, background-color 0.3s;
      min-width: 250px;
    }

    .btn:hover {
      background-color: rgba(0, 0, 0, 0.2);
      transform: scale(1.05);
    }

    .btn:active {
      transform: scale(0.98);
    }

    @media (min-width: 600px) {
      .btn {
        font-size: 1.2em;
        min-width: 300px;
      }
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal.show {
      display: flex;
    }

    .modal-content {
      background-color: white;
      padding: 30px;
      border-radius: 12px;
      position: relative;
      width: 90%;
      max-width: 700px;
      max-height: 90vh;
      overflow: auto;
      text-align: left;
      animation: fadeIn 0.3s ease forwards;
      transform: scale(0.9);
      opacity: 0;
    }

    .modal.fade-out .modal-content {
      animation: fadeOut 0.3s ease forwards;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      font-weight: bold;
      color: #000;
      cursor: pointer;
      background: none;
      border: none;
    }

    .map-image {
      width: 100%;
      height: auto;
    }

    /* Animations */
    @keyframes fadeIn {
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: scale(0.9);
      }
    }
  </style>
</head>
<body>

  <h2>Virtual Tour Kampus</h2>
  <img src="images/logo.png" alt="Logo Politeknik Caltex Riau" class="logo"/>

  <div class="buttons">
    <button class="btn" onclick="openMap()">Mulai Virtual Tour</button>
    <button class="btn" onclick="openManual()">Manual</button>
  </div>

  <!-- Manual Modal -->
  <div class="modal" id="manualModal">
    <div class="modal-content">
      <button class="close-btn" onclick="closeManual()">×</button>
      <h3>Manual Virtual Tour</h3>
      <ol>
        <li>Bergerak menggunakan tombol keyboard W (maju), A (ke kiri), S (ke kanan), D (mundur)</li>
        <li>Arahkan kamera dengan menggunakan mouse</li>
        <li>Pada Virtual Tour, dapat berpindah dengan cepat menggunakan menu panel di bawah kanan</li>
      </ol>
    </div>
  </div>

  <!-- Map Modal -->
  <div class="modal" id="mapModal">
    <div class="modal-content">
      <button class="close-btn" onclick="closeMap()">×</button>
      <h3>Peta Kampus - Klik Lokasi</h3>
      <img src="images/map.png" alt="Peta Kampus" usemap="#campusmap" class="map-image" />
      <map name="campusmap">
        <area shape="rect" coords="50,50,200,200" href="index.php" alt="Gedung A" />
        <area shape="rect" coords="220,50,370,200" href="index.php" alt="Gedung B" />
        <area shape="rect" coords="390,50,540,200" href="index.php" alt="Perpustakaan" />
      </map>
      <!-- DEBUG: Remove this div for deployment -->
      <div id="debug-coords" style="position:absolute; right:20px; bottom:15px; background:rgba(0,0,0,0.7); color:#fff; padding:4px 10px; border-radius:6px; font-size:14px; z-index:10; pointer-events:none; display:none;">
        x: 0, y: 0
      </div>
      <!-- END DEBUG -->
    </div>
  </div>

  <script>
    function openModal(id) {
      const modal = document.getElementById(id);
      modal.classList.add("show");
      modal.classList.remove("fade-out");
    }

    function closeModal(id) {
      const modal = document.getElementById(id);
      modal.classList.add("fade-out");
      setTimeout(() => {
        modal.classList.remove("show");
        modal.classList.remove("fade-out");
      }, 300);
    }

    function openManual() {
      openModal('manualModal');
    }

    function closeManual() {
      closeModal('manualModal');
    }

    function openMap() {
      openModal('mapModal');
    }

    function closeMap() {
      closeModal('mapModal');
    }

    // Close modal if clicking outside
    window.onclick = function(event) {
      const manualModal = document.getElementById('manualModal');
      const mapModal = document.getElementById('mapModal');
      if (event.target === manualModal) closeManual();
      if (event.target === mapModal) closeMap();
    }

    // === DEBUG: Show mouse coordinates on map image (remove for deployment) ===
    document.addEventListener('DOMContentLoaded', function() {
      const img = document.querySelector('#mapModal .map-image');
      const coordsDiv = document.getElementById('debug-coords');
      if (img && coordsDiv) {
        img.addEventListener('mousemove', function(e) {
          const rect = img.getBoundingClientRect();
          const scaleX = img.naturalWidth / img.width;
          const scaleY = img.naturalHeight / img.height;
          const x = Math.round((e.clientX - rect.left) * scaleX);
          const y = Math.round((e.clientY - rect.top) * scaleY);
          coordsDiv.textContent = `x: ${x}, y: ${y}`;
          coordsDiv.style.display = 'block';
        });
        img.addEventListener('mouseleave', function() {
          coordsDiv.style.display = 'none';
        });
      }
    });
    // === END DEBUG