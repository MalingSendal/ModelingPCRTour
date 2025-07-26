# 🎓 PCR Virtual Campus Tour ![Status](https://img.shields.io/badge/status-in--development-yellow) ![License](https://img.shields.io/badge/license-Educational-blue) ![Made with Three.js](https://img.shields.io/badge/3D-Three.js-ff69b4)

Welcome to the **Virtual Tour Kampus Politeknik Caltex Riau**!  
This project is an interactive 3D virtual tour of the PCR campus, allowing users to explore buildings and facilities directly from their browser.

![PCR Logo](images/logo.png)

---

## 🚀 Features

- **Interactive 3D Navigation:** Jelajahi area kampus menggunakan kontrol keyboard dan mouse.
- **Teleportasi:** Lompat langsung ke lokasi-lokasi kunci melalui panel navigasi.
- **Peta Kampus:** Mulai perjalanan dengan memilih lokasi dari peta kampus yang interaktif.
- **Antarmuka Responsif:** Tampilan yang modern dan bersih untuk desktop dan mobile.
- **Codebase Modular:** Struktur kode PHP yang terorganisir untuk memudahkan pemeliharaan dan perluasan fitur.

---

## 🏗️ Struktur Proyek

```
.
├── .gitignore                  # Aturan untuk mengabaikan file yang tidak perlu di Git
├── debug_virtual_tour.php      # Versi debug dari virtual tour
├── gsg.php                     # Tour 3D untuk Gedung Serba Guna
├── halaman.html                # Halaman HTML statis (referensi tambahan)
├── index.php                   # Halaman depan dengan peta kampus
├── kantin.php                  # Tour 3D untuk Kantin
├── masjid.php                  # Tour 3D untuk Masjid
├── README.md                   # Dokumentasi dan gambaran proyek
├── school.glb                  # Model 3D sekolah (jika digunakan)
├── sport.php                   # Tour 3D untuk Sport Hall
├── test.txt                    # File uji (untuk commit testing)
├── utama.php                   # Tour 3D untuk Gedung Utama
├── virtual_tour.php            # Titik masuk untuk virtual tour
├── workshop.php                # Tour 3D untuk Workshop
├── assets/                     # Model 3D (.glb)
├── images/                     # Logo, peta, dan gambar-gambar lainnya
└── info/                       # Gambar info untuk hotspot
```

---

## 🕹️ Kontrol

- **Gerak:** `W` (maju), `A` (kiri), `S` (mundur), `D` (kanan)
- **Lihat Sekeliling:** Drag mouse
- **Teleportasi:** Gunakan panel navigasi untuk berpindah ke lokasi tertentu
- **Kembali ke Menu:** Klik tombol pada panel navigasi

---

## 📈 Progress Pengembangan

| Fitur                     | Status         |
|---------------------------|---------------|
| Navigasi 3D               | ✅ Implementasi selesai  |
| Panel Teleportasi         | ✅ Implementasi selesai  |
| Peta Kampus               | ✅ Implementasi selesai  |
| Multi Gedung              | ✅ Implementasi selesai  |
| Deteksi Tabrakan          | ⏳ Dalam pengembangan   |
| Ruang Dalam Gedung        | ⏳ Direncanakan         |
| Hotspot Audio/Info        | ⏳ Direncanakan         |

---

## 🛠️ Memulai Proyek

1. **Clone repository ini** dan siapkan server web PHP pada lingkungan lokal Anda.
2. **Pastikan environment Anda** dapat mengakses Three.js via CDN.
3. **Akses situs** melalui URL lokal (misal: http://localhost/).

---

## 📦 Dependencies

- [Three.js](https://threejs.org/) (via CDN)
- GLTFLoader (untuk memuat file `.glb`)

---

## 🤝 Kontribusi

Kontribusi sangat dianjurkan!  
Silahkan buka issue atau submit pull request seiring perkembangan proyek.

---

## 📄 Lisensi

Proyek ini digunakan untuk tujuan pendidikan di Politeknik Caltex Riau.

---

_Made by Zulhardika Rendy Permana_