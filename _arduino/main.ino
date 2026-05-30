#include <ArduinoJson.h>
#include <DHT.h>
#include <HTTPClient.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <time.h>

const char *ssid = "Maison";
const char *password = "SANGATLUXU";

const char *laravelEndpointData = "https://jamkot.sfht.space/api/sensor/data";
const char *laravelEndpointSchedule = "https://jamkot.sfht.space/api/schedule";
const char *laravelEndpointPumpStatus = "https://jamkot.sfht.space/api/pump/status";
const char *laravelEndpointHeartbeat = "https://jamkot.sfht.space/api/device/status";

WiFiClientSecure secureClient;

const long gmtOffset_sec = 7 * 3600;
const int daylightOffset_sec = 0;

#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

#define LDR_PIN 34
#define RELAY_KIPAS 26
#define RELAY_POMPA 27

// Konfigurasi jenis relay (Ganti ke true jika relay menyala saat diberi sinyal HIGH, ganti ke false jika menyala saat LOW)
const bool RELAY_KIPAS_ACTIVE_HIGH = false;
const bool RELAY_POMPA_ACTIVE_HIGH = false;

// Kalibrasi Otomatis & Karakteristik LDR (Sistem menyesuaikan secara dinamis)
int ldrAdcDark = 4095;                 // Nilai ADC saat tergelap (menyesuaikan dinamis)
int ldrAdcBright = 3700;               // Nilai ADC saat ruangan normal (menyesuaikan dinamis jika mendapat cahaya lebih terang)
const float LDR_CURVE_EXPONENT = 0.45; // Mengangkat sensitivitas cahaya di kondisi ruangan biasa/redup
int currentRawLDR = 0;

unsigned long lastScheduleUpdate = 0;
const unsigned long scheduleUpdateInterval = 3600000;

unsigned long lastPumpStatusUpdate = 0;
const unsigned long pumpStatusUpdateInterval = 1000;

unsigned long lastSensorRead = 0;
const unsigned long sensorReadInterval = 15000;

unsigned long lastHeartbeat = 0;
const unsigned long heartbeatInterval = 60000; // Heartbeat setiap 1 menit

float batasSuhuPanas = 30.0;
float batasKelembapanKering = 78.0;

int pagi_mulai = 0, pagi_selesai = 0;
int siang_mulai = 0, siang_selesai = 0;
int sore_mulai = 0, sore_selesai = 0;

String manualPumpStatus = "OFF";
float currentT = 0.0;
float currentH = 0.0;
int currentCahaya = 0;

// Mengonversi waktu string (HH:MM) ke menit
int timeToMinutes(String timeStr) {
  if (timeStr.length() < 5)
    return -1;
  int h = timeStr.substring(0, 2).toInt();
  int m = timeStr.substring(3, 5).toInt();
  return (h * 60) + m;
}

// Mengambil data jadwal penyiraman dari server
void fetchScheduleFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("[HTTP] Fetching watering schedule from server...");
    HTTPClient http;
    http.begin(secureClient, laravelEndpointSchedule);

    int httpResponseCode = http.GET();
    if (httpResponseCode == 200) {
      String payload = http.getString();

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
        
        Serial.printf("       -> Pagi  : %s - %s\n", data["pagi_mulai"].as<const char*>(), data["pagi_selesai"].as<const char*>());
        Serial.printf("       -> Siang : %s - %s\n", data["siang_mulai"].as<const char*>(), data["siang_selesai"].as<const char*>());
        Serial.printf("       -> Sore  : %s - %s\n", data["sore_mulai"].as<const char*>(), data["sore_selesai"].as<const char*>());
        Serial.printf("       -> Limit Kelembapan: %.1f%%\n", batasKelembapanKering);
      }
    } else {
      Serial.printf("[HTTP] Fetch FAILED. Code: %d\n", httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("[HTTP] Fetch FAILED (WiFi Disconnected)");
  }
}

// Mengambil status manual pompa dari server
void fetchPumpStatusFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(secureClient, laravelEndpointPumpStatus);
    int httpResponseCode = http.GET();

    if (httpResponseCode == 200) {
      String payload = http.getString();

      DynamicJsonDocument doc(256);
      deserializeJson(doc, payload);
      String newStatus = doc["status"].as<String>();

      if (newStatus != manualPumpStatus) {
        manualPumpStatus = newStatus;
        Serial.printf("\n[PUMP] Web status changed -> %s\n", manualPumpStatus.c_str());
      }
    } else {
      // Tidak log error di sini agar tidak membanjiri Serial Monitor setiap detik
    }
    http.end();
  }
}

// Mengirim data sensor ke server
void sendDataToWeb(String statusPompa) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(secureClient, laravelEndpointData);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{";
    jsonPayload += "\"sensor_id\":\"JAMKOT-01\",";
    jsonPayload += "\"suhu\":" + String(currentT) + ",";
    jsonPayload += "\"kelembapan\":" + String(currentH) + ",";
    jsonPayload += "\"cahaya\":" + String(currentCahaya) + ",";
    jsonPayload += "\"pompa_status\":\"" + statusPompa + "\"";
    jsonPayload += "}";

    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
      Serial.printf("  -> Response: Success (Code: %d)\n", httpResponseCode);
    } else {
      Serial.printf("  -> Response: FAILED (%s)\n", http.errorToString(httpResponseCode).c_str());
    }
    http.end();
  } else {
    Serial.println("  -> Response: FAILED (WiFi Disconnected)");
  }
}

#ifdef __cplusplus
extern "C" {
#endif
uint8_t temprature_sens_read();
#ifdef __cplusplus
}
#endif

float readESP32Temp() {
  #if defined(ESP32)
    uint8_t raw = temprature_sens_read();
    return (raw - 32) / 1.8;
  #else
    return 0.0;
  #endif
}

void sendHeartbeat() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(secureClient, laravelEndpointHeartbeat);
    http.addHeader("Content-Type", "application/json");

    float h = dht.readHumidity();
    float t = dht.readTemperature();
    bool dhtOk = !isnan(h) && !isnan(t);
    
    int ldrRaw = analogRead(LDR_PIN);
    bool ldrOk = (ldrRaw > 50 && ldrRaw < 4050);

    float espTemp = readESP32Temp();

    DynamicJsonDocument doc(256);
    doc["device_id"]      = "ESP32-JAMKOT";
    doc["ip_address"]     = WiFi.localIP().toString();
    doc["uptime_seconds"] = millis() / 1000;
    doc["dht_connected"]  = dhtOk;
    doc["ldr_connected"]  = ldrOk;
    doc["free_heap"]      = ESP.getFreeHeap();
    doc["rssi"]           = WiFi.RSSI();
    doc["esp_temp"]       = espTemp;

    String jsonPayload;
    serializeJson(doc, jsonPayload);

    int httpResponseCode = http.POST(jsonPayload);
    if (httpResponseCode > 0) {
      Serial.printf("[HEARTBEAT] Sent. Temp: %.1f C, RSSI: %d dBm, Code: %d\n", espTemp, WiFi.RSSI(), httpResponseCode);
    } else {
      Serial.printf("[HEARTBEAT] Send FAILED (%s)\n", http.errorToString(httpResponseCode).c_str());
    }
    http.end();
  }
}

// Inisialisasi perangkat keras dan koneksi sistem
void setup() {
  Serial.begin(115200);
  delay(500); // Jeda pendek agar inisialisasi serial port stabil
  Serial.println("\n--------------------------------------------------");
  Serial.println("JAMUR AUTOMATION MONITORING & KONTROL OVER TELEMETRI");
  Serial.println("--------------------------------------------------");

  pinMode(RELAY_KIPAS, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);
  digitalWrite(RELAY_KIPAS, RELAY_KIPAS_ACTIVE_HIGH ? LOW : HIGH); // Matikan kipas saat startup (HIGH untuk Active-Low)
  digitalWrite(RELAY_POMPA, RELAY_POMPA_ACTIVE_HIGH ? LOW : HIGH); // Matikan pompa saat startup (HIGH untuk Active-Low)

  secureClient.setInsecure(); // Konfigurasi global secureClient agar mengabaikan verifikasi SSL certificate

  dht.begin();
  pinMode(LDR_PIN, INPUT);

  Serial.printf("[WIFI] Connecting to '%s'...", ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\n[WIFI] Connected!");
  Serial.print("       IP Address: ");
  Serial.println(WiFi.localIP());

  Serial.print("[TIME] NTP Sync...");
  configTime(gmtOffset_sec, daylightOffset_sec, "pool.ntp.org");
  struct tm timeinfo;
  while (!getLocalTime(&timeinfo, 5000)) {
    Serial.print(".");
  }
  Serial.println(" Success!");

  fetchScheduleFromWeb();

  // Ambil data sensor awal (diberikan jeda 1 detik agar DHT siap)
  delay(1000);
  float h = dht.readHumidity();
  float t = dht.readTemperature();
  if (!isnan(h) && !isnan(t)) {
    currentH = h;
    currentT = t;
  }
  currentRawLDR = analogRead(LDR_PIN);
  // Lindungi batas pembacaan ADC ESP32
  if (currentRawLDR < 100) { currentRawLDR = 100; }
  if (currentRawLDR > 4095) { currentRawLDR = 4095; }
  
  // Auto-kalibrasi dinamis awal
  if (currentRawLDR < ldrAdcBright) { ldrAdcBright = currentRawLDR; }
  if (currentRawLDR > ldrAdcDark) { ldrAdcDark = currentRawLDR; }
  if (ldrAdcDark - ldrAdcBright < 300) { ldrAdcDark = ldrAdcBright + 300; }

  float normLDR = (float)(ldrAdcDark - currentRawLDR) / (ldrAdcDark - ldrAdcBright);
  normLDR = constrain(normLDR, 0.0, 1.0);
  currentCahaya = (int)(pow(normLDR, LDR_CURVE_EXPONENT) * 100.0);

  Serial.println("\n[SYS ] JAMKOT System Ready.");
  Serial.println("--------------------------------------------------");
  sendHeartbeat();
}

// Loop utama sistem
void loop() {
  unsigned long currentMillis = millis();

  if (currentMillis - lastScheduleUpdate >= scheduleUpdateInterval) {
    lastScheduleUpdate = currentMillis;
    fetchScheduleFromWeb();
  }

  if (currentMillis - lastPumpStatusUpdate >= pumpStatusUpdateInterval) {
    lastPumpStatusUpdate = currentMillis;
    fetchPumpStatusFromWeb();
  }

  if (lastHeartbeat == 0 || currentMillis - lastHeartbeat >= heartbeatInterval) {
    lastHeartbeat = currentMillis;
    sendHeartbeat();
  }

  struct tm timeinfo;
  getLocalTime(&timeinfo, 10);

  String actualPumpStatus = "OFF";

  // Kontrol Kipas berdasarkan Suhu
  if (currentT > batasSuhuPanas) {
    digitalWrite(RELAY_KIPAS, RELAY_KIPAS_ACTIVE_HIGH ? HIGH : LOW);
  } else {
    digitalWrite(RELAY_KIPAS, RELAY_KIPAS_ACTIVE_HIGH ? LOW : HIGH);
  }

  // Kontrol Pompa berdasarkan Status Manual Web
  if (manualPumpStatus == "ON") {
    digitalWrite(RELAY_POMPA, RELAY_POMPA_ACTIVE_HIGH ? HIGH : LOW);
    actualPumpStatus = "ON";
  } else {
    digitalWrite(RELAY_POMPA, RELAY_POMPA_ACTIVE_HIGH ? LOW : HIGH);
    actualPumpStatus = "OFF";
  }

  if (lastSensorRead == 0 || currentMillis - lastSensorRead >= sensorReadInterval) {
    lastSensorRead = currentMillis;

    float h = dht.readHumidity();
    float t = dht.readTemperature();
    if (!isnan(h) && !isnan(t)) {
      currentH = h;
      currentT = t;
    }
    currentRawLDR = analogRead(LDR_PIN);
    // Lindungi batas pembacaan ADC ESP32
    if (currentRawLDR < 100) { currentRawLDR = 100; }
    if (currentRawLDR > 4095) { currentRawLDR = 4095; }

    // Auto-kalibrasi dinamis (belajar batas gelap dan terang secara real-time)
    if (currentRawLDR < ldrAdcBright) { ldrAdcBright = currentRawLDR; }
    if (currentRawLDR > ldrAdcDark) { ldrAdcDark = currentRawLDR; }
    if (ldrAdcDark - ldrAdcBright < 300) { ldrAdcDark = ldrAdcBright + 300; }

    float normLDR = (float)(ldrAdcDark - currentRawLDR) / (ldrAdcDark - ldrAdcBright);
    normLDR = constrain(normLDR, 0.0, 1.0);
    int targetCahaya = (int)(pow(normLDR, LDR_CURVE_EXPONENT) * 100.0);
    // Filter rata-rata bergerak eksponensial (Exponential Smoothing) agar transisi mulus dan stabil
    currentCahaya = (currentCahaya * 0.7) + (targetCahaya * 0.3);

    String actualKipasStatus = (currentT > batasSuhuPanas) ? "NYALA" : "MATI";

    Serial.printf("\n>>> TELEMETRY REPORT [%02d:%02d]\n", timeinfo.tm_hour, timeinfo.tm_min);
    Serial.printf("  Suhu       : %.1f C  (Batas: %.1f C)\n", currentT, batasSuhuPanas);
    Serial.printf("  Kelembapan : %.1f %%  (Batas: %.1f %%)\n", currentH, batasKelembapanKering);
    Serial.printf("  Cahaya     : %d %%  (Raw ADC: %d, Rentang: %d - %d)\n", currentCahaya, currentRawLDR, ldrAdcBright, ldrAdcDark);
    Serial.printf("  Kipas Fan  : %s\n", actualKipasStatus.c_str());
    Serial.printf("  Pompa Web  : %s  |  Pompa Fisik : %s\n", manualPumpStatus.c_str(), actualPumpStatus.c_str());
    Serial.println("  --------------------------------------------------");
    Serial.println("  [DATA] Uploading telemetry to server...");
    sendDataToWeb(actualPumpStatus);
    Serial.println(">>> END REPORT\n");
  }

  delay(50);
}