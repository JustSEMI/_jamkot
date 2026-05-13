# JAMKOT End-to-End Architecture: Kontrol Pompa Manual

Sistem penyiraman JAMKOT sekarang tidak hanya beroperasi secara otomatis mengikuti jadwal dan kelembapan, tetapi juga memiliki fitur **Overrides (Kendali Manual Instan)** langsung dari Web Dashboard.

Dokumen ini menjelaskan alur kerja (*End-to-End*) fitur Kontrol Pompa Manual beserta potongan kode yang digunakan di setiap tahapnya.

---

## 1. Penyimpanan Status (Database)

Kita membutuhkan tempat untuk menyimpan instruksi yang ditekan oleh pengguna di Web agar bisa dibaca oleh ESP32. Kolom `manual_pump_status` bertipe ENUM (`AUTO`, `ON`, `OFF`) ditambahkan pada tabel `schedules`.

**File:** `database/migrations/..._add_manual_pump_status_to_schedules_table.php`
```php
public function up(): void
{
    Schema::table('schedules', function (Blueprint $table) {
        $table->enum('manual_pump_status', ['AUTO', 'ON', 'OFF'])->default('AUTO');
    });
}
```

---

## 2. API Komunikasi (Laravel Backend)

Terdapat dua fungsi utama di sisi server:
1. Endpoint menerima klik dari Web (`/panel/pump/toggle`).
2. Endpoint super ringan untuk *polling* ESP32 (`/api/pump/status`).

**File:** `app/Http/Controllers/PanelController.php` (Menerima dari Web)
```php
public function togglePump(Request $request)
{
    $jadwal = Schedule::first();
    $request->validate(['status' => 'required|in:AUTO,ON,OFF']);

    // Simpan status baru (ON/AUTO) ke database
    $jadwal->manual_pump_status = $request->status;
    $jadwal->save();

    return response()->json(['status' => 'success']);
}
```

**File:** `app/Http/Controllers/Api/ScheduleController.php` (Dibaca oleh ESP32)
```php
public function getPumpStatus(Request $request)
{
    $jadwal = Schedule::first();
    return response()->json([
        'status' => $jadwal->manual_pump_status ?? 'AUTO'
    ]);
}
```

---

## 3. Optimistic UI & Sinkronisasi (Frontend JavaScript)

Tombol di Web harus memberikan respons seketika ketika diklik, lalu mengirim instruksi di latar belakang. Selain itu, layar akan otomatis menyesuaikan *(sync)* apabila pengguna lain mengubah status pompa.

**File:** `public/js/realtime.js` (Logika Optimistic Toggle)
```javascript
function togglePumpOptimistic() {
    isPumpOn = !isPumpOn;
    // ... update teks & warna tombol ke layar secara instan ...

    // Kirim permintaan di latar belakang
    fetch('/panel/pump/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: isPumpOn ? 'ON' : 'AUTO' })
    }).catch(error => {
        alert("Gagal menghubungi server.");
        window.location.reload(); // Revert ke state sebenarnya jika gagal
    });
}
```

**File:** `public/js/realtime.js` (Auto-Sync dengan Polling Realtime)
```javascript
// Terjadi setiap 5 detik saat fetchRealtimeData dipanggil
if (btnText && stateLabel && indicatorDot) {
    isPumpOn = (manualPumpStatus === 'ON'); // Setel variabel sesuai database

    if (isPumpOn) {
        btnText.innerText = "MATIKAN";
        stateLabel.innerText = "ON";
        // (Warna Hijau)
    } else {
        btnText.innerText = "NYALAKAN";
        stateLabel.innerText = "OFF";
        // (Warna Abu-abu)
    }
}
```

---

## 4. Logika Pintar Firmware (ESP32 Arduino)

Di sisi perangkat keras, sistem dirombak menggunakan fungsi *Non-Blocking Timers* berbasis `millis()`. Alat akan terus berjalan ribuan putaran per detik tanpa harus "tertidur" menggunakan `delay(60000)`.

**File:** `_arduino/main.ino`

### A. Polling Rutin Tanpa Henti (Non-Blocking)
```cpp
void loop() {
  unsigned long currentMillis = millis();

  // Tiap 5 Detik: Tanya server apakah ada yang memencet tombol
  if (currentMillis - lastPumpStatusUpdate >= 5000) {
    lastPumpStatusUpdate = currentMillis;
    fetchPumpStatusFromWeb(); // Mengubah variabel manualPumpStatus
  }

  // Tiap 1 Menit: Baca sensor (Suhu, Kelembapan, Cahaya) & Kirim ke Web
  if (currentMillis - lastSensorRead >= 60000) { ... }
}
```

### B. Hierarki Keputusan (Aktuator)
Setiap saat, ESP32 memutuskan apakah relai dinyalakan berdasarkan urutan prioritas yang ketat.
```cpp
  // PRIORITAS TERTINGGI: Perintah Manual dari Web
  if (manualPumpStatus == "ON") {
    digitalWrite(RELAY_POMPA, LOW); 
    actualPumpStatus = "ON";
  } 
  else if (manualPumpStatus == "OFF") {
    digitalWrite(RELAY_POMPA, HIGH);
    actualPumpStatus = "OFF";
  } 
  // PRIORITAS STANDAR: Mode Otomatis
  else {
    bool isScheduleActive = ... // (Cek rentang jam)

    // Menyala JIKA sedang masuk jadwal ATAU kelembapan sangat kering
    if (isScheduleActive || currentH < batasKelembapanKering) {
      digitalWrite(RELAY_POMPA, LOW); 
      actualPumpStatus = "ON";
    } else {
      digitalWrite(RELAY_POMPA, HIGH);
      actualPumpStatus = "OFF";
    }
  }
```

### C. Pelaporan Status Valid
Setelah keputusan dibuat, status asli relai dicatat di `actualPumpStatus` dan dikirim kembali bersama data sensor ke Laravel. Ini memastikan apa yang tertulis di Database 100% sama dengan apa yang terjadi pada *hardware* secara nyata.
```cpp
    jsonPayload += "\"pompa_status\":\"" + actualPumpStatus + "\"";
    http.POST(jsonPayload);
```

---

*Arsitektur melingkar (Circular Loop) inilah yang memungkinkan kendali real-time bekerja harmonis tanpa harus mengorbankan stabilitas pengukuran dan pengiriman data sensor.*
