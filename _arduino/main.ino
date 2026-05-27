#include <ArduinoJson.h>
#include <DHT.h>
#include <HTTPClient.h>
#include <WiFi.h>
#include <time.h>

const char *ssid = "Maison";
const char *password = "SANGATLUXU";

const char *laravelEndpointData = "http://localhost:8000/api/sensor/data";
const char *laravelEndpointSchedule = "http://localhost:8000/api/schedule";
const char *laravelEndpointPumpStatus = "http://localhost:8000/api/pump/status";

const long gmtOffset_sec = 7 * 3600;
const int daylightOffset_sec = 0;

#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

#define LDR_PIN 34
#define RELAY_KIPAS 26
#define RELAY_POMPA 27

unsigned long lastScheduleUpdate = 0;
const unsigned long scheduleUpdateInterval = 3600000;

unsigned long lastPumpStatusUpdate = 0;
const unsigned long pumpStatusUpdateInterval = 1000;

unsigned long lastSensorRead = 0;
const unsigned long sensorReadInterval = 15000;

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

// Mengambil status manual pompa dari server
void fetchPumpStatusFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(laravelEndpointPumpStatus);
    int httpResponseCode = http.GET();

    if (httpResponseCode == 200) {
      String payload = http.getString();

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

// Mengirim data sensor ke server
void sendDataToWeb(String statusPompa) {
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[API POST] Menyiapkan pengiriman data ke server...");
    HTTPClient http;
    http.begin(laravelEndpointData);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{";
    jsonPayload += "\"sensor_id\":\"JAMKOT-01\",";
    jsonPayload += "\"suhu\":" + String(currentT) + ",";
    jsonPayload += "\"kelembapan\":" + String(currentH) + ",";
    jsonPayload += "\"cahaya\":" + String(currentCahaya) + ",";
    jsonPayload += "\"pompa_status\":\"" + statusPompa + "\"";
    jsonPayload += "}";

    Serial.println(">> Payload JSON yang dikirim: " + jsonPayload);

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

// Inisialisasi perangkat keras dan koneksi sistem
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

  Serial.print("Menghubungkan ke WiFi: ");
  Serial.print(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\n[SUKSES] Terhubung ke Jaringan!");
  Serial.print(">> IP Address ESP32: ");
  Serial.println(WiFi.localIP());
  Serial.println("-----------------------------------------");

  Serial.print("Sinkronisasi waktu NTP...");
  configTime(gmtOffset_sec, daylightOffset_sec, "pool.ntp.org");
  struct tm timeinfo;
  while (!getLocalTime(&timeinfo, 5000)) {
    Serial.println("Gagal NTP, mencoba lagi...");
  }
  Serial.println(" [SUKSES]");

  fetchScheduleFromWeb();

  currentH = dht.readHumidity();
  currentT = dht.readTemperature();
  currentCahaya = (analogRead(LDR_PIN) / 4095.0) * 100;

  Serial.println("\n[SISTEM] JAMKOT SIAP TEMPUR!");
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

  struct tm timeinfo;
  int currentMinutes = -1;
  if (getLocalTime(&timeinfo, 10)) {
    currentMinutes = (timeinfo.tm_hour * 60) + timeinfo.tm_min;
  }

  String actualPumpStatus = "OFF";

  if (currentT > batasSuhuPanas) {
    digitalWrite(RELAY_KIPAS, LOW);
  } else {
    digitalWrite(RELAY_KIPAS, HIGH);
  }

  if (manualPumpStatus == "ON") {
    digitalWrite(RELAY_POMPA, LOW);
    actualPumpStatus = "ON";
  } else {
    digitalWrite(RELAY_POMPA, HIGH);
    actualPumpStatus = "OFF";
  }

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

  delay(50);
}