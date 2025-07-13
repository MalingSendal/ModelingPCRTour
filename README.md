# 🎓 PCR Virtual Campus Tour ![Status](https://img.shields.io/badge/status-in--development-yellow) ![License](https://img.shields.io/badge/license-Educational-blue) ![Made with Three.js](https://img.shields.io/badge/3D-Three.js-ff69b4)

Welcome to the **Virtual Tour Kampus Politeknik Caltex Riau**!  
This project is an interactive 3D virtual tour of the PCR campus, allowing users to explore buildings and facilities directly from their browser.

<p align="center">
  <img src="images/logo.png" alt="PCR Logo" width="200"/>
</p>

---

## 🚀 Features

- **Interactive 3D Navigation:** Move around the campus using keyboard and mouse controls.
- **Teleportation:** Instantly jump to key locations via the side panel.
- **Campus Map:** Start your journey by selecting locations from a visual campus map.
- **Responsive UI:** Clean and modern interface, works on desktop and mobile.
- **Modular Codebase:** Organized PHP includes for easy maintenance and extension.

---

## 🏗️ Project Structure

```
.
├── index.php         # Main landing page with campus map
├── utama.php         # Gedung Utama 3D tour
├── kantin.php        # Kantin 3D tour
├── masjid.php        # Masjid 3D tour
├── gsg.php           # Gedung Serba Guna 3D tour
├── workshop.php      # Workshop 3D tour
├── sport.php         # Sport Hall 3D tour
├── includes/         # PHP includes (scene, UI, controls, teleport, etc.)
├── images/           # Logos, maps, and other images
├── assets/           # 3D models (.glb)
└── README.md
```

---

## 🕹️ Controls

- **Move:** `W` (forward), `A` (left), `S` (back), `D` (right)
- **Look Around:** Mouse drag
- **Teleport:** Use the side panel to jump to locations
- **Back to Menu:** Use the button in the side panel

---

## 📈 Development Progress

| Feature                | Status         |
|------------------------|---------------|
| 3D Navigation          | ✅ Implemented |
| Teleportation Panel    | ✅ Implemented |
| Campus Map             | ✅ Implemented |
| Multiple Buildings     | ✅ Implemented |
| Collision Detection    | ⏳ In Progress |
| Mobile Optimization    | ⏳ In Progress |
| Building Interiors     | ⏳ Planned     |
| Audio/Info Hotspots    | ⏳ Planned     |

---

## 🛠️ Getting Started

1. **Clone this repository**
2. **Run with a local PHP server:**
   ```sh
   php -S localhost:8080
   ```
3. **Open** [http://localhost:8080](http://localhost:8080) in your browser.

---

## 📦 Dependencies

- [Three.js](https://threejs.org/) (via CDN)
- GLTFLoader (for loading `.glb` models)

---

## 🤝 Contributing

Contributions are welcome!  
Feel free to open issues or submit pull requests as the project evolves.

---

## 📄 License

This project is for educational purposes at Politeknik Caltex Riau.

---

<p align="center">
  <img src="https://img.shields.io/badge/Made%20by-Zulhardika%20Rendy%20Permana-blueviolet?style=for-the