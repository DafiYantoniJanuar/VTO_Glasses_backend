# Implementation Plan: AR Virtual Try-On Kacamata

## 1. Ringkasan Project

**Nama Project:** VTO Glasses — Virtual Try-On Kacamata Berbasis Web
**Tujuan:** Membuat aplikasi web yang memungkinkan pengguna mencoba (try-on) berbagai model kacamata secara virtual menggunakan kamera, mirip fitur AR filter di Instagram/Snapchat, tapi khusus untuk produk kacamata dengan katalog dan fitur e-commerce dasar.

**Target Pengguna:** Simulasi toko optik online — pengguna bisa browse katalog kacamata, coba pakai secara virtual lewat kamera, simpan favorit, dan checkout dummy.

**Prinsip Utama:** Semua tool dan library yang digunakan **100% gratis**, tidak ada API berbayar, tidak ada biaya langganan.

---

## 2. Fitur yang Diharapkan

### 2.1 Fitur Inti (Wajib Ada)

| No | Fitur | Deskripsi |
|---|---|---|
| 1 | **Live Camera Try-On** | Kamera aktif di browser, mendeteksi wajah secara real-time |
| 2 | **Overlay Kacamata Otomatis** | Kacamata menempel dan mengikuti posisi wajah (termasuk saat kepala miring/gerak) |
| 3 | **Ganti Model Kacamata** | User bisa geser/klik untuk ganti-ganti model kacamata saat kamera aktif |
| 4 | **Katalog Produk** | Halaman list kacamata lengkap dengan nama, harga, deskripsi, gambar preview |
| 5 | **Capture/Screenshot** | Ambil foto hasil try-on dan download/simpan |
| 6 | **Halaman Detail Produk** | Info lengkap tiap kacamata + tombol "Coba Sekarang" langsung ke kamera |

### 2.2 Fitur Pelengkap (Menambah Nilai)

| No | Fitur | Deskripsi |
|---|---|---|
| 7 | **Sistem Favorit/Wishlist** | Simpan kacamata yang disukai untuk dilihat lagi nanti |
| 8 | **Autentikasi User** | Login/register sederhana (biar wishlist & histori tersimpan per user) |
| 9 | **Dashboard Admin** | CRUD produk kacamata: upload gambar, upload model 3D, atur harga & stok |
| 10 | **Share Hasil Try-On** | Share hasil capture ke media sosial atau download sebagai gambar |
| 11 | **Filter & Search Katalog** | Filter berdasarkan kategori (kacamata baca, kacamata hitam, dll), bentuk, warna |
| 12 | **Riwayat Try-On** | Simpan histori produk apa saja yang pernah dicoba user |

### 2.3 Fitur Bonus (Jika Waktu Memungkinkan)

| No | Fitur | Deskripsi |
|---|---|---|
| 13 | **Deteksi Bentuk Wajah** | Sistem menganalisis bentuk wajah (oval/bulat/kotak) dan merekomendasikan model kacamata yang cocok |
| 14 | **Checkout Dummy** | Simulasi keranjang belanja & checkout (tanpa payment gateway asli) |
| 15 | **Rating & Review** | User bisa kasih rating/ulasan produk |
| 16 | **Mode Perbandingan** | Bandingkan 2 model kacamata berdampingan (split screen) |

---

## 3. Third-Party & Tools (Semua Gratis)

### 3.1 Untuk Fitur AR / Face Tracking

| Tool | Fungsi | Link | Catatan |
|---|---|---|---|
| **MediaPipe Face Mesh** (Google) | Mendeteksi 468 titik landmark wajah secara real-time | [developers.google.com/mediapipe](https://developers.google.com/mediapipe) | Open-source, jalan sepenuhnya di browser (client-side), tidak kirim data ke server manapun |
| **Three.js** | Render model kacamata 3D dan sinkronisasi posisi/rotasi dengan wajah | [threejs.org](https://threejs.org) | Open-source, dokumentasi lengkap |
| **WebRTC (`getUserMedia`)** | Mengakses kamera device lewat browser | Built-in browser API | Tidak perlu install apapun, sudah tersedia di semua browser modern |

> **Alternatif lebih ringan (jika tim kesulitan dengan 3D):** gunakan **face-api.js** untuk deteksi wajah + overlay gambar PNG 2D biasa pakai Canvas API. Lebih mudah dipelajari, cocok untuk MVP di minggu-minggu awal.

### 3.2 Untuk Model 3D Kacamata

| Tool | Fungsi | Link | Catatan |
|---|---|---|---|
| **Sketchfab** | Sumber model 3D `.glb`/`.gltf` kacamata | [sketchfab.com](https://sketchfab.com) | Filter "Downloadable" + cek lisensi CC0/CC-BY sebelum pakai |
| **Blender** (opsional) | Edit/buat model 3D sendiri jika perlu modifikasi | [blender.org](https://blender.org) | Gratis, open-source, tapi butuh waktu belajar |

### 3.3 Untuk Backend & Database

| Tool | Fungsi | Catatan |
|---|---|---|
| **Laravel** | Framework backend (sesuai stack yang sudah familiar) | Gratis, open-source |
| **MySQL** | Database | Gratis |
| **Laravel Sanctum** | Autentikasi API sederhana (untuk login/register) | Built-in Laravel, gratis |

### 3.4 Untuk Frontend

| Tool | Fungsi | Catatan |
|---|---|---|
| **React** atau **Vue** | Framework frontend | Pilih salah satu sesuai kenyamanan tim |
| **Inertia.js** (opsional) | Menghubungkan Laravel + React/Vue tanpa bikin API terpisah | Cocok kalau mau lebih cepat development, sudah pernah dipakai di project POS |
| **Tailwind CSS** | Styling | Gratis, cepat untuk prototyping UI |

### 3.5 Untuk Storage File (Gambar & Model 3D)

| Tool | Fungsi | Free Tier |
|---|---|---|
| **Laravel Storage (local)** | Simpan file di server sendiri | Gratis, paling simpel untuk skala PKL |
| **Cloudinary** (opsional) | Cloud storage untuk gambar/model 3D jika ingin lebih ringan di server | Free tier: 25 GB storage & bandwidth/bulan |
| **Firebase Storage** (alternatif) | Sama seperti Cloudinary | Free tier tersedia (Spark Plan) |

> **Rekomendasi:** untuk skala project PKL, cukup pakai **Laravel local storage** dulu. Tidak perlu setup cloud storage kecuali ukuran file jadi masalah.

### 3.6 Untuk Deployment (Opsional, jika ingin di-hosting online)

| Tool | Fungsi | Free Tier |
|---|---|---|
| **Railway** / **Render** | Hosting backend Laravel | Free tier tersedia (dengan batasan resource) |
| **Vercel** / **Netlify** | Hosting frontend (jika dipisah dari backend) | Free tier generous |

---

## 4. Skema Database (Draft Awal)

```
users
- id, name, email, password, created_at

products (kacamata)
- id, name, description, price, category, 
  thumbnail_image, model_3d_url (glb file), 
  stock, created_at

categories
- id, name (contoh: "Kacamata Hitam", "Kacamata Baca", "Kacamata Minus")

favorites
- id, user_id, product_id, created_at

try_on_history
- id, user_id, product_id, captured_image_url, created_at

reviews (bonus)
- id, user_id, product_id, rating, comment, created_at
```

---

## 5. Alur Kerja Utama (User Flow)

1. User membuka halaman **Katalog** → melihat list kacamata
2. User klik salah satu produk → masuk ke **Halaman Detail**
3. User klik tombol **"Coba Sekarang"** → kamera aktif, sistem mendeteksi wajah
4. Kacamata otomatis muncul menempel di wajah, mengikuti gerakan kepala
5. User bisa **geser kiri/kanan** untuk ganti model kacamata lain tanpa keluar dari mode kamera
6. User klik **capture** → foto tersimpan, bisa didownload atau dishare
7. User bisa **simpan ke favorit** dari halaman detail atau saat mode try-on
8. (Bonus) User bisa checkout dummy untuk kacamata yang dipilih

---

## 6. Roadmap Mingguan (Estimasi 8-10 Minggu)

| Minggu | Fokus |
|---|---|
| 1 | Riset teknis, setup project (Laravel + React/Vue), pelajari dasar MediaPipe/face-api.js, kumpulkan/cari model 3D atau gambar kacamata |
| 2 | Setup database, buat CRUD produk dasar (backend), buat halaman katalog (frontend, tanpa AR dulu) |
| 3 | Implementasi kamera dasar (`getUserMedia`) + deteksi wajah (MediaPipe/face-api.js), tampilkan landmark wajah di layar (belum overlay kacamata) |
| 4 | Implementasi overlay kacamata 2D PNG mengikuti posisi wajah (MVP versi sederhana) |
| 5 | Testing & perbaikan kalibrasi posisi overlay (skala, rotasi mengikuti gerakan kepala) |
| 6 | Upgrade ke overlay 3D dengan Three.js (jika tim sudah nyaman), atau lanjut perbaiki versi 2D jika waktu terbatas |
| 7 | Implementasi fitur capture/screenshot, sistem favorit, autentikasi user |
| 8 | Dashboard admin (CRUD produk, upload gambar/model 3D) |
| 9 | Testing menyeluruh di berbagai device, perbaikan bug, optimasi performa |
| 10 | Polish UI/UX, dokumentasi project, persiapan presentasi/demo akhir |

> Roadmap ini fleksibel — kalau fitur AR (minggu 3-6) ternyata lebih cepat selesai, sisa waktu bisa dipakai untuk fitur bonus (deteksi bentuk wajah, checkout dummy, dll).

---

## 7. Panduan Menggunakan AI Coding Assistant

Karena project ini akan banyak dibantu AI (Claude Code, Cursor, dll), ikuti pola berikut supaya proses lebih terarah:

1. **Jangan langsung minta "buatkan semua"** — pecah tugas per fitur kecil (contoh: "buatkan fungsi untuk menghitung posisi overlay berdasarkan landmark mata kiri dan kanan dari MediaPipe")
2. **Selalu kasih konteks yang jelas** saat bertanya ke AI: sertakan library yang dipakai, potongan kode yang relevan, dan error message lengkap jika sedang debug
3. **Pahami logika dasar sebelum lanjut** — terutama bagian transformasi posisi (translate, rotate, scale), karena ini akan sering perlu di-tweak manual berdasarkan hasil testing visual
4. **AI tidak bisa "melihat" hasil di kamera** — jadi tim harus selalu testing manual dan melaporkan hasil visual ke AI ("kacamata terlalu besar saat wajah dekat kamera") untuk mendapat saran perbaikan
5. Gunakan AI juga untuk **code review** sebelum menggabungkan kode dari tiap anggota tim, supaya gaya coding tetap konsisten

---

## 8. Referensi Belajar Gratis

- **MediaPipe Face Mesh Documentation:** https://developers.google.com/mediapipe/solutions/vision/face_landmarker
- **Three.js Fundamentals:** https://threejsfundamentals.org
- **face-api.js GitHub (contoh & dokumentasi):** https://github.com/justadudewhohacks/face-api.js
- **Sketchfab (model 3D gratis):** https://sketchfab.com/search?features=downloadable&type=models

---

## 9. Catatan Penting

- Pastikan setiap model 3D atau aset gambar yang diambil dari internet **memiliki lisensi yang jelas** (CC0 atau CC-BY) agar tidak melanggar hak cipta, terutama jika project ini akan dipresentasikan atau dimasukkan ke portofolio publik.
- Semua library dan tool di atas dipilih khusus karena **gratis dan open-source** — tidak ada risiko biaya tak terduga selama pengerjaan.
- Jika di tengah jalan fitur 3D terasa terlalu berat untuk tim, tidak masalah turun ke versi 2D overlay — yang penting fungsi try-on tetap bekerja dan meyakinkan secara visual.
