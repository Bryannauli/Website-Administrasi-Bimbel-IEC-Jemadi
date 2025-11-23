<p align="center">
  <img src="public/images/IEC.png" width="180" alt="Logo IEC Jemadi">
</p>

<p align="center">
  <h2 align="center">Website Administrasi Bimbel IEC Jemadi</h2>
</p>

<p align="center">
  Sistem administrasi internal untuk pengelolaan data siswa, kelas, jadwal, dan kegiatan operasional Bimbingan Belajar IEC Jemadi.
</p>

---

## Tentang Projek

Website Administrasi Bimbel IEC Jemadi adalah sistem berbasis web yang dirancang untuk membantu pengelolaan tugas administrasi bimbingan belajar, meliputi:

- Manajemen data siswa
- Pengelompokan kelas dan tutor
- Penjadwalan pembelajaran
- Pencatatan perkembangan akademik dan kehadiran siswa

Tujuan dari sistem ini adalah meningkatkan kerapian data, efisiensi pencatatan, dan mempermudah proses administrasi secara keseluruhan.

---

## Tech Stack

| Teknologi | Keterangan |
|----------|------------|
| **Laravel** | Backend & Web Framework |
| **Laravel Breeze** | Starter kit untuk autentikasi (Login, Register, Session) |
| **MySQL** | Database |
| **Blade** | Frontend |
| **Tailwind** | Styling |
| **XAMPP / Laragon** | Local Development Server |

---

## Anggota Tim

| NIM | Nama | Peran |
|----|------|-------|
| **241402036** | Ahmad Arif Fatahillah | Project Manager, Front-End Developer |
| **241402043** | Rahma Sarita Nasution | UI/UX Designer |
| **241402066** | Agnes Olivia Ketaren | Front-End Developer |
| **241402073** | Richard Lim | Back-End Developer |
| **241402108** | Bryan Sulivan Nauli | Back-End Developer |

---

## Cara Instalasi & Menjalankan Projek

### 1. Clone Repository
```bash
git clone https://github.com/Bryannauli/Website-Administrasi-Bimbel-IEC-Jemadi.git
cd Website-Administrasi-Bimbel-IEC-Jemadi
```

### 2. Install Dependency Backend
```bash
composer install
```

### 3. Install Dependency Frontend
```bash
npm install
```

### 4. Copy File Environment
```bash
cp .env.example .env
```

### 5. Konfigurasi File `.env`
Uncomment dan ubah sesuai database lokal:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iec_jemadi
DB_USERNAME=root
DB_PASSWORD=
```

#### **Credential Khusus Role**
Tambahkan variabel dibawah ini untuk koneksi database role tertentu:
```env
DB_IEC_ADMIN_USERNAME=iec_admin
DB_IEC_ADMIN_PASSWORD=IEC_Jemadi_Admin

DB_IEC_TEACHER_USERNAME=iec_teacher
DB_IEC_TEACHER_PASSWORD=IEC_Teacher123
```
> Pastikan nilai di atas **sinkron dengan seeder pengguna** agar login awal berfungsi.

### 6. Generate Application Key
```bash
php artisan key:generate
```

### 7. Setup Database & Data Awal (Command Custom)
Menjalankan migrasi + data awal (Direkomendasikan)
```bash
php artisan migrate:setup --seed
```
Jika hanya ingin migrasi (database kosong)
```bash
php artisan migrate:setup
```
Saat seeding berhasil, Anda bisa login dengan akun:
- Username: admin
- password: admin123

### 8. Jalankan Frontend (Development)
```bash
npm run dev
```

### 9. Jalankan Server Laravel
```bash
php artisan serve
```
