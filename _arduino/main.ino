#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <time.h>
#include <ArduinoJson.h>

// CONNECT TO YOUR WIFI
const char* ssid = "YOUR_SSID"; 
const char* password = "YOUR_PASSWORD"; 

// REST API (GANTI DENGAN IP KOMPUTER ATAU DOMAIN HOSTING ANDA)
const char* laravelEndpointData = "http://YOUR_DOMAIN_OR_IP/api/sensor/data";
const char* laravelEndpointSchedule = "http://YOUR_DOMAIN_OR_IP/api/schedule";
const char* laravelEndpointPumpStatus = "http://YOUR_DOMAIN_OR_IP/api/pump/status"; // Endpoint baru

// NTP TIME SERVER (WIB = UTC+7)
const long  gmtOffset_sec = 7 * 3600;
const int   daylightOffset_sec = 0;

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
const unsigned long pumpStatusUpdateInterval = 5000; // Cek status manual tiap 5 detik

unsigned long lastSensorRead = 0;
const unsigned long sensorReadInterval = 60000; // Baca sensor & kirim web tiap 60 detik

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
  if (timeStr.length() < 5) return -1;
  int h = timeStr.substring(0, 2).toInt();
  int m = timeStr.substring(3, 5).toInt();
  return (h * 60) + m;
}

// FUNGSI 1: AMBIL JADWAL
void fetchScheduleFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("[API] Sinkronisasi jadwal...");
    HTTPClient http;
    http.begin(laravelEndpointSchedule);
    
    int httpResponseCode = http.GET();
    if (httpResponseCode == 200) {
      DynamicJsonDocument doc(1024);
      deserializeJson(doc, http.getString());
      if (String(doc["status"] | "") == "SUCCESS") {
        JsonObject data = doc["data"];
        pagi_mulai = timeToMinutes(data["pagi_mulai"].as<String>());
        pagi_selesai = timeToMinutes(data["pagi_selesai"].as<String>());
        siang_mulai = timeToMinutes(data["siang_mulai"].as<String>());
        siang_selesai = timeToMinutes(data["siang_selesai"].as<String>());
        sore_mulai = timeToMinutes(data["sore_mulai"].as<String>());
        sore_selesai = timeToMinutes(data["sore_selesai"].as<String>());
        if (data.containsKey("batas_kelembapan")) batasKelembapanKering = data["batas_kelembapan"].as<float>();
      }
    }
    http.end();
  }
}

// FUNGSI 2: AMBIL STATUS POMPA MANUAL
void fetchPumpStatusFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(laravelEndpointPumpStatus);
    int httpResponseCode = http.GET();
    if (httpResponseCode == 200) {
      DynamicJsonDocument doc(256);
      deserializeJson(doc, http.getString());
      String newStatus = doc["status"].as<String>();
      if (newStatus != manualPumpStatus) {
        manualPumpStatus = newStatus;
        Serial.println(">> [PERINTAH WEB] Status Pompa berubah menjadi: " + manualPumpStatus);
      }
    }
    http.end();
  }
}

// FUNGSI 3: KIRIM DATA SENSOR
void sendDataToWeb(String statusPompa) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(laravelEndpointData);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{";
    jsonPayload += "\"sensor_id\":\"ESP32_DEV_1\",";
    jsonPayload += "\"suhu\":" + String(currentT) + ",";
    jsonPayload += "\"kelembapan\":" + String(currentH) + ",";
    jsonPayload += "\"cahaya\":" + String(currentCahaya) + ",";
    jsonPayload += "\"pompa_status\":\"" + statusPompa + "\"";
    jsonPayload += "}";
    
    http.POST(jsonPayload);
    http.end();
  }
}

void setup() {
  Serial.begin(115200);
  Serial.println("\n JAMKOT READY ");

  pinMode(RELAY_KIPAS, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);
  digitalWrite(RELAY_KIPAS, HIGH); 
  digitalWrite(RELAY_POMPA, HIGH); 

  dht.begin();
  pinMode(LDR_PIN, INPUT);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) { delay(500); Serial.print("."); }
  Serial.println("\n[SUKSES] Terhubung ke WiFi!");

  configTime(gmtOffset_sec, daylightOffset_sec, "pool.ntp.org");
  struct tm timeinfo;
  while (!getLocalTime(&timeinfo, 5000)) { Serial.println("Gagal NTP..."); }
  
  fetchScheduleFromWeb();
  
  // Baca pertama kali agar tidak 0
  currentH = dht.readHumidity();
  currentT = dht.readTemperature();
  currentCahaya = (analogRead(LDR_PIN) / 4095.0) * 100;
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
  if (getLocalTime(&timeinfo)) {
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
    // Paksa nyala dari Web
    digitalWrite(RELAY_POMPA, LOW); 
    actualPumpStatus = "ON";
  } else if (manualPumpStatus == "OFF") {
    // Paksa mati dari Web
    digitalWrite(RELAY_POMPA, HIGH);
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

  // 4. BACA SENSOR & KIRIM DATA KE WEB (Tiap 60 Detik)
  if (currentMillis - lastSensorRead >= sensorReadInterval) {
    lastSensorRead = currentMillis;
    
    float h = dht.readHumidity();
    float t = dht.readTemperature();
    if (!isnan(h) && !isnan(t)) {
      currentH = h;
      currentT = t;
      currentCahaya = (analogRead(LDR_PIN) / 4095.0) * 100;
    }
    
    Serial.printf("\n[LOG] Waktu: %02d:%02d | Suhu: %.1f°C | Kelembapan: %.1f%% | Manual: %s | Pompa Aktual: %s\n", 
                  timeinfo.tm_hour, timeinfo.tm_min, currentT, currentH, manualPumpStatus.c_str(), actualPumpStatus.c_str());

    sendDataToWeb(actualPumpStatus);
  }

  // Delay super singkat agar ESP32 tidak panas
  delay(50);
}