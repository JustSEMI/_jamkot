#include <ArduinoJson.h>
#include <DHT.h>
#include <HTTPClient.h>
#include <WiFi.h>
#include <time.h>

// CONNECT TO YOUR WIFI
const char *ssid = "Maison";
const char *password = "SANGATLUXU";

// REST API - VERSI LOCAL (GANTI IP SESUAI IP WIFI PC KAMU, MISAL: 192.168.1.4)
const char *laravelEndpointData = "http://localhost:8000/api/sensor/data";
const char *laravelEndpointSchedule = "http://localhost:8000/api/schedule";
const char *laravelEndpointPumpStatus = "http://localhost:8000/api/pump/status";

// REST API - VERSI PRODUCTION (VERCEL)
// Uncomment baris di bawah ini dan comment versi local jika ingin ke Vercel:
// const char *laravelEndpointData = "https://jamkot.vercel.app/api/sensor/data";
// const char *laravelEndpointSchedule = "https://jamkot.vercel.app/api/schedule";
// const char *laravelEndpointPumpStatus = "https://jamkot.vercel.app/api/pump/status";

// NTP TIME SERVER (WIB = UTC+7)
const long gmtOffset_sec = 7 * 3600;
const int daylightOffset_sec = 0;

// PIN DEFINITIONS
#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

#define LDR_PIN 34
#define RELAY_KIPAS 26
#define RELAY_POMPA 27

// TIMERS (NON-BLOCKING)
unsigned long lastScheduleUpdate = 0;
const unsigned long scheduleUpdateInterval = 3600000; // 1 Jam

unsigned long lastPumpStatusUpdate = 0;
const unsigned long pumpStatusUpdateInterval =
    5000; // Cek status manual tiap 5 detik

unsigned long lastSensorRead = 0;
const unsigned long sensorReadInterval =
    15000; // Baca sensor & kirim web tiap 15 detik

// VARIABLES
float batasSuhuPanas = 30.0;
float batasKelembapanKering = 78.0;

int pagi_mulai = 0, pagi_selesai = 0;
int siang_mulai = 0, siang_selesai = 0;
int sore_mulai = 0, sore_selesai = 0;

String manualPumpStatus = "AUTO"; // 'AUTO', 'ON', 'OFF'
float currentT = 0.0;
float currentH = 0.0;
int currentCahaya = 0;

int timeToMinutes(String timeStr) {
  if (timeStr.length() < 5)
    return -1;
  int h = timeStr.substring(0, 2).toInt();
  int m = timeStr.substring(3, 5).toInt();
  return (h * 60) + m;
}

// FUNGSI 1: AMBIL JADWAL
void fetchScheduleFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[API GET] Meminta data jadwal dari server...");
    HTTPClient http;
    http.begin(laravelEndpointSchedule);

    int httpResponseCode = http.GET();
    if (httpResponseCode == 200) {
      String payload = http.getString();
      Serial.println(">> [SUKSES] Jadwal diterima: " + payload);

      DynamicJsonDocument doc(1024);
      deserializeJson(doc, payload);
      if (String(doc["status"] | "") == "SUCCESS") {
        JsonObject data = doc["data"];
        pagi_mulai = timeToMinutes(data["pagi_mulai"].as<String>());
        pagi_selesai = timeToMinutes(data["pagi_selesai"].as<String>());
        siang_mulai = timeToMinutes(data["siang_mulai"].as<String>());
        siang_selesai = timeToMinutes(data["siang_selesai"].as<String>());
        sore_mulai = timeToMinutes(data["sore_mulai"].as<String>());
        sore_selesai = timeToMinutes(data["sore_selesai"].as<String>());
        if (data.containsKey("batas_kelembapan"))
          batasKelembapanKering = data["batas_kelembapan"].as<float>();
        Serial.println(
            ">> [INFO] Variabel jadwal berhasil diperbarui di memori ESP32.");
      }
    } else {
      Serial.printf(">> [ERROR] Gagal mengambil jadwal. HTTP Code: %d\n",
                    httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("\n[ERROR] WiFi Terputus! Tidak bisa sinkron jadwal.");
  }
}

// FUNGSI 2: AMBIL STATUS POMPA MANUAL
void fetchPumpStatusFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(laravelEndpointPumpStatus);
    int httpResponseCode = http.GET();

    if (httpResponseCode == 200) {
      String payload = http.getString();
      // Un-comment baris di bawah ini kalau lu mau lihat log JSON pompa tiap 5
      // detik (agak spam sih) Serial.println("\n[API GET] Status Pompa: " +
      // payload);

      DynamicJsonDocument doc(256);
      deserializeJson(doc, payload);
      String newStatus = doc["status"].as<String>();

      if (newStatus != manualPumpStatus) {
        manualPumpStatus = newStatus;
        Serial.println("\n[PERINGATAN!] >>> Status Pompa berubah menjadi: " +
                       manualPumpStatus + " <<<");
      }
    } else {
      Serial.printf("\n[ERROR] Gagal cek status pompa manual. HTTP Code: %d\n",
                    httpResponseCode);
    }
    http.end();
  }
}

// FUNGSI 3: KIRIM DATA SENSOR
void sendDataToWeb(String statusPompa) {
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[API POST] Menyiapkan pengiriman data ke server...");
    HTTPClient http;
    http.begin(laravelEndpointData);
    http.addHeader("Content-Type", "application/json");

    // Merakit JSON
    String jsonPayload = "{";
    jsonPayload +=
        "\"sensor_id\":\"JAMKOT-01\","; // Sesuaikan dengan ID di database lu
    jsonPayload += "\"suhu\":" + String(currentT) + ",";
    jsonPayload += "\"kelembapan\":" + String(currentH) + ",";
    jsonPayload += "\"cahaya\":" + String(currentCahaya) + ",";
    jsonPayload += "\"pompa_status\":\"" + statusPompa + "\"";
    jsonPayload += "}";

    Serial.println(">> Payload JSON yang dikirim: " + jsonPayload);

    // Eksekusi pengiriman
    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
      Serial.printf(">> [SUKSES] Server menerima data. HTTP Code: %d\n",
                    httpResponseCode);
      String response = http.getString();
      Serial.println(">> Respon dari Laravel: " + response);
    } else {
      Serial.printf(">> [GAGAL] Error ngirim HTTP POST: %s\n",
                    http.errorToString(httpResponseCode).c_str());
    }
    http.end();
  } else {
    Serial.println("\n[ERROR] WiFi Terputus! Data gagal dikirim.");
  }
}

void setup() {
  Serial.begin(115200);
  Serial.println("\n=========================================");
  Serial.println("🔥 MEMULAI SISTEM JAMKOT REAL-TIME 🔥");
  Serial.println("=========================================");

  pinMode(RELAY_KIPAS, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);
  digitalWrite(RELAY_KIPAS, HIGH);
  digitalWrite(RELAY_POMPA, HIGH);

  dht.begin();
  pinMode(LDR_PIN, INPUT);

  // Konek WiFi
  Serial.print("Menghubungkan ke WiFi: ");
  Serial.print(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\n[SUKSES] Terhubung ke Jaringan!");
  Serial.print(">> IP Address ESP32: ");
  Serial.println(WiFi.localIP()); // <-- NAMPILIN IP ADDRESS DI SINI
  Serial.println("-----------------------------------------");

  // Setup Waktu
  Serial.print("Sinkronisasi waktu NTP...");
  configTime(gmtOffset_sec, daylightOffset_sec, "pool.ntp.org");
  struct tm timeinfo;
  while (!getLocalTime(&timeinfo, 5000)) {
    Serial.println("Gagal NTP, mencoba lagi...");
  }
  Serial.println(" [SUKSES]");

  // Tarik data awal
  fetchScheduleFromWeb();

  // Baca sensor pertama kali agar tidak bernilai 0 di awal
  currentH = dht.readHumidity();
  currentT = dht.readTemperature();
  currentCahaya = (analogRead(LDR_PIN) / 4095.0) * 100;

  Serial.println("\n[SISTEM] JAMKOT SIAP TEMPUR!");
}

void loop() {
  unsigned long currentMillis = millis();

  // 1. POLLING JADWAL (Tiap 1 Jam)
  if (currentMillis - lastScheduleUpdate >= scheduleUpdateInterval) {
    lastScheduleUpdate = currentMillis;
    fetchScheduleFromWeb();
  }

  // 2. POLLING STATUS MANUAL POMPA (Tiap 5 Detik)
  if (currentMillis - lastPumpStatusUpdate >= pumpStatusUpdateInterval) {
    lastPumpStatusUpdate = currentMillis;
    fetchPumpStatusFromWeb();
  }

  // 3. LOGIKA AKTUATOR (KIPAS & POMPA - Dieksekusi sangat cepat)
  struct tm timeinfo;
  int currentMinutes = -1;
  if (getLocalTime(&timeinfo, 10)) {
    currentMinutes = (timeinfo.tm_hour * 60) + timeinfo.tm_min;
  }

  String actualPumpStatus = "OFF";

  // -- KIPAS LOGIC
  if (currentT > batasSuhuPanas) {
    digitalWrite(RELAY_KIPAS, LOW); // ON
  } else {
    digitalWrite(RELAY_KIPAS, HIGH); // OFF
  }

  // -- POMPA LOGIC
  if (manualPumpStatus == "ON") {
    // Manual ON dari Web - Paksa Menyala
    digitalWrite(RELAY_POMPA, LOW); // ON
    actualPumpStatus = "ON";
  } else if (manualPumpStatus == "OFF") {
    // Paksa mati dari Web - Paksa Mati
    digitalWrite(RELAY_POMPA, HIGH); // OFF
    actualPumpStatus = "OFF";
  } else {
    // AUTO MODE (Jadwal / Kelembapan)
    bool isScheduleActive = false;
    if (currentMinutes != -1) {
      if ((currentMinutes >= pagi_mulai && currentMinutes <= pagi_selesai) ||
          (currentMinutes >= siang_mulai && currentMinutes <= siang_selesai) ||
          (currentMinutes >= sore_mulai && currentMinutes <= sore_selesai)) {
        isScheduleActive = true;
      }
    }

    if (isScheduleActive || currentH < batasKelembapanKering) {
      digitalWrite(RELAY_POMPA, LOW); // ON
      actualPumpStatus = "ON";
    } else {
      digitalWrite(RELAY_POMPA, HIGH); // OFF
      actualPumpStatus = "OFF";
    }
  }

  // 4. BACA SENSOR & KIRIM DATA KE WEB (Tiap 15 Detik)
  if (currentMillis - lastSensorRead >= sensorReadInterval) {
    lastSensorRead = currentMillis;

    float h = dht.readHumidity();
    float t = dht.readTemperature();
    if (!isnan(h) && !isnan(t)) {
      currentH = h;
      currentT = t;
      currentCahaya = (analogRead(LDR_PIN) / 4095.0) * 100;
    }

    Serial.println("\n=========================================");
    Serial.printf("[SENSOR LOG] Waktu: %02d:%02d\n", timeinfo.tm_hour,
                  timeinfo.tm_min);
    Serial.printf("Suhu: %.1f°C | Kelembapan: %.1f%% | Cahaya: %d%%\n",
                  currentT, currentH, currentCahaya);
    Serial.printf("Mode Manual Web: %s | Status Asli Pompa: %s\n",
                  manualPumpStatus.c_str(), actualPumpStatus.c_str());
    Serial.println("=========================================");

    sendDataToWeb(actualPumpStatus);
  }

  // Delay super singkat agar ESP32 tidak kepanasan
  delay(50);
}