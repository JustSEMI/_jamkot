# JAMKOT - Smart Monitoring and Automation System

[![Laravel Version](https://img.shields.io/badge/Laravel-v13-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.3-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Tailwind Version](https://img.shields.io/badge/TailwindCSS-v4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Vite Version](https://img.shields.io/badge/Vite-v8-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vite.dev)
[![Alpine Version](https://img.shields.io/badge/Alpine.js-v3-8BC0D0?style=flat-square&logo=alpine.js&logoColor=black)](https://alpinejs.dev)
[![Pest Version](https://img.shields.io/badge/Pest-v4-019639?style=flat-square&logo=pest&logoColor=white)](https://pestphp.com)

Bilingual Documentation / Dokumentasi Dwibahasa:
- [Bahasa Indonesia](#bahasa-indonesia)
- [English](#english)

---

## Bahasa Indonesia

JAMKOT adalah aplikasi web pemantauan IoT dan otomatisasi real-time yang dibangun menggunakan framework Laravel. Aplikasi ini menyediakan panel visual untuk memantau parameter lingkungan (suhu, kelembapan, dan intensitas cahaya) serta mengontrol aktuator (pompa air misting) baik secara otomatis maupun manual.

### Fitur

- [+] Dashboard Lingkungan Real-Time: Menampilkan data langsung suhu, kelembapan, dan intensitas cahaya (Lux) dari sensor DHT22 dan LDR.
- [+] Kontrol Aktuator: Tombol sakelar manual dan konfigurasi jadwal untuk mengontrol pompa misting.
- [+] Visualisasi Data Langsung: Grafik data historis interaktif yang didukung oleh ApexCharts.
- [+] Analisis Data dan Pelaporan: Menghitung metrik minimum, maksimum, dan rata-rata, dengan dukungan ekspor log historis terfilter ke format CSV dan PDF.
- [+] Visualisasi Perangkat 3D: Representasi visual dari wadah perangkat keras dalam lingkungan 3D.
- [+] Pemilih Tema UI Premium: Dukungan untuk tema gelap minimalis yang bersih dan sistem desain Material 3.
- [+] Pengujian dan Pemformatan Otomatis: Integrasi framework pengujian Pest dan pemformatan kode Laravel Pint.

### Teknologi

* **Backend**:
  * PHP 8.3 &emsp; [![PHP](https://img.shields.io/badge/-PHP-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
  * Laravel 13 &emsp; [![Laravel](https://img.shields.io/badge/-Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
* **Database**:
  * MySQL / SQLite &emsp; [![MySQL](https://img.shields.io/badge/-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
* **Frontend**:
  * TailwindCSS v4 &emsp; [![TailwindCSS](https://img.shields.io/badge/-TailwindCSS-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
  * Alpine.js &emsp; [![Alpine.js](https://img.shields.io/badge/-Alpine.js-8BC0D0?style=flat-square&logo=alpine.js&logoColor=black)](https://alpinejs.dev)
  * Vite &emsp; [![Vite](https://img.shields.io/badge/-Vite-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vite.dev)
* **Pengujian**:
  * Pest PHP &emsp; [![Pest](https://img.shields.io/badge/-PestPHP-019639?style=flat-square&logo=pest&logoColor=white)](https://pestphp.com)

### Instalasi dan Konfigurasi

Ikuti langkah-langkah berikut untuk mengonfigurasi dan menjalankan aplikasi secara lokal:

#### 1. Prasyarat

Pastikan Anda telah menginstal perangkat lunak berikut di sistem Anda:
- PHP 8.3 atau versi lebih tinggi
- Composer
- Node.js dan NPM
- Server database (MySQL, MariaDB, atau SQLite)

#### 2. Kloning Repositori

Kloning proyek ke ruang kerja lokal Anda:
```bash
git clone <repository-url>
cd _jamkot
```

#### 3. Instal Dependensi

Instal paket komposer PHP dan dependensi Node NPM:
```bash
composer install
npm install
```

#### 4. Konfigurasi Lingkungan

Salin file contoh konfigurasi lingkungan dan sesuaikan setelan database Anda:
```bash
copy .env.example .env
```
Buka file `.env` dan perbarui konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jamkot_db
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Generate Kunci Aplikasi

Buat kunci enkripsi aplikasi Laravel:
```bash
php artisan key:generate
```

#### 6. Migrasi Database dan Seeding

Jalankan migrasi untuk membuat tabel dan mengisi data awal (seperti setelan konfigurasi dan ambang batas target):
```bash
php artisan migrate --seed
```

#### 7. Jalankan Aplikasi

Jalankan server pengembangan lokal PHP dan kompilasi aset:
- Jalankan server Laravel:
  ```bash
  php artisan serve
  ```
- Jalankan server pengembangan Vite:
  ```bash
  npm run dev
  ```

Buka browser Anda dan navigasikan ke alamat default (biasanya http://127.0.0.1:8000).

### Gaya Kode dan Pengujian

Untuk menjaga konsistensi dan perilaku kode yang benar di seluruh aplikasi:

#### Pemformatan Kode

Format file PHP menggunakan Laravel Pint:
```bash
vendor/bin/pint --format agent
```

#### Menjalankan Pengujian

Jalankan rangkaian pengujian Pest:
```bash
php artisan test --compact
```

### Sorotan Struktur Direktori

```
[dir]  app/Http/Controllers/  - Controller rute aplikasi (misalnya, PanelController).
[dir]  resources/views/        - Tampilan Blade untuk layout, dashboard, rincian sensor, dll.
[dir]  public/css/             - Stylesheet kustom (termasuk tema gelap dan material 3).
[dir]  public/js/              - Javascript inti untuk polling, grafik, dan custom select.
[dir]  tests/                  - Rangkaian pengujian otomatis Unit dan Feature.
```

---

## English

JAMKOT is a real-time IoT monitoring and automation web application built on the Laravel framework. It provides a visual panel for monitoring environmental parameters (temperature, humidity, and ambient light) and controlling actuators (water misting pump) both automatically and manually.

### Features

- [+] Real-Time Environmental Dashboard: Displays live feeds of temperature, humidity, and light intensity (Lux) from DHT22 and LDR sensors.
- [+] Actuator Control: Manual toggle switches and schedule configurations to control the misting pump.
- [+] Live Data Visualization: Interactive historical data charts powered by ApexCharts.
- [+] Data Analysis and Reporting: Computes minimum, maximum, and average metrics, with support for exporting filtered historical logs to CSV and PDF formats.
- [+] 3D Device Visualization: Visual representation of the hardware device container in a 3D environment.
- [+] Premium UI Theme Selector: Support for clean minimalist dark themes and Material 3 design systems.
- [+] Automated Testing and Formatting: Integrated Pest testing framework and Laravel Pint code formatting.

### Technology Stack

* **Backend**:
  * PHP 8.3 &emsp; [![PHP](https://img.shields.io/badge/-PHP-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
  * Laravel 13 &emsp; [![Laravel](https://img.shields.io/badge/-Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
* **Database**:
  * MySQL / SQLite &emsp; [![MySQL](https://img.shields.io/badge/-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
* **Frontend**:
  * TailwindCSS v4 &emsp; [![TailwindCSS](https://img.shields.io/badge/-TailwindCSS-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
  * Alpine.js &emsp; [![Alpine.js](https://img.shields.io/badge/-Alpine.js-8BC0D0?style=flat-square&logo=alpine.js&logoColor=black)](https://alpinejs.dev)
  * Vite &emsp; [![Vite](https://img.shields.io/badge/-Vite-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vite.dev)
* **Testing**:
  * Pest PHP &emsp; [![Pest](https://img.shields.io/badge/-PestPHP-019639?style=flat-square&logo=pest&logoColor=white)](https://pestphp.com)

### Installation and Setup

Follow these steps to set up and run the application locally:

#### 1. Prerequisites

Ensure you have the following installed on your system:
- PHP 8.3 or higher
- Composer
- Node.js and NPM
- Database server (MySQL, MariaDB, or SQLite)

#### 2. Clone the Repository

Clone the project to your local workspace:
```bash
git clone <repository-url>
cd _jamkot
```

#### 3. Install Dependencies

Install the PHP composer packages and Node NPM dependencies:
```bash
composer install
npm install
```

#### 4. Environment Configuration

Copy the example environment file and configure your database settings:
```bash
copy .env.example .env
```
Open the `.env` file and update the database configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jamkot_db
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Generate Application Key

Generate the Laravel application encryption key:
```bash
php artisan key:generate
```

#### 6. Database Migration and Seeding

Run the migrations to create tables and seed default data (such as configuration settings and target thresholds):
```bash
php artisan migrate --seed
```

#### 7. Run the Application

Start the local PHP development server and compile the assets:
- Start Laravel server:
  ```bash
  php artisan serve
  ```
- Start Vite development server:
  ```bash
  npm run dev
  ```

Open your browser and navigate to the default address (usually http://127.0.0.1:8000).

### Code Style and Testing

To maintain consistency and correct behavior across the codebase:

#### Code Formatting

Format PHP files using Laravel Pint:
```bash
vendor/bin/pint --format agent
```

#### Running Tests

Execute the Pest test suite:
```bash
php artisan test --compact
```

### Directory Structure Highlights

```
[dir]  app/Http/Controllers/  - Application route controllers (e.g., PanelController).
[dir]  resources/views/        - Blade template views for layouts, dashboard, settings, etc.
[dir]  public/css/             - Custom styling stylesheets (including dark themes).
[dir]  public/js/              - Core Javascript drivers for polling, charts, and custom selects.
[dir]  tests/                  - Automated Unit and Feature test suites.
```
