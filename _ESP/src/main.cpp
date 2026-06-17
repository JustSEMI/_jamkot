#include <ArduinoJson.h>
#include <DHT.h>
#include <HTTPClient.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <time.h>
#include <stdarg.h>

// CONFIG
const char *ssid = "Maison";
const char *password = "SANGATLUXU";

const char *laravelEndpointData = "https://jamkot.sfht.space/api/sensor/data";
const char *laravelEndpointSchedule = "https://jamkot.sfht.space/api/schedule";
const char *laravelEndpointPumpStatus = "https://jamkot.sfht.space/api/pump/status";
const char *laravelEndpointHeartbeat = "https://jamkot.sfht.space/api/device/status";

WiFiClientSecure secureClientPump;
WiFiClientSecure secureClientTelemetry;
WiFiClientSecure secureClientGeneral;
WiFiClientSecure secureClientHeartbeat;

HTTPClient httpPump;
HTTPClient httpTelemetry;

const long gmtOffset_sec = 7 * 3600;
const int daylightOffset_sec = 0;

#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

#define LDR_PIN 34
#define RELAY_KIPAS 26
#define RELAY_POMPA 27

const bool RELAY_KIPAS_ACTIVE_HIGH = false;
const bool RELAY_POMPA_ACTIVE_HIGH = false;

const float LDR_R_FIXED  = 100000.0; // 100kΩ (pull-up)
const float LDR_R10      = 100000.0; // 10 lux (calibrate as needed)
const float LDR_ALPHA    = 0.6;      // (calibrate as needed)
const int   LDR_SAMPLES  = 64;       // ADC averaging samples
const float LDR_VCC_MV   = 3300.0;   // supply voltage in mV
const float LDR_V_MAX_MV = 3100.0;   // Practical ADC ceiling (mV)
float currentLux = 0.0;
unsigned long lastScheduleUpdate = 0;
const unsigned long scheduleUpdateInterval = 3600000;
unsigned long lastPumpStatusUpdate = 0;
const unsigned long pumpStatusUpdateInterval = 1000;
unsigned long lastSensorRead = 0;
const unsigned long sensorReadInterval = 15000;
unsigned long lastHeartbeat = 0;
const unsigned long heartbeatInterval = 60000;
float batasSuhuPanas = 30.0;
float batasKelembapanKering = 78.0;
int pagi_mulai = 0, pagi_selesai = 0;
int siang_mulai = 0, siang_selesai = 0;
int sore_mulai = 0, sore_selesai = 0;
String manualPumpStatus = "OFF";
float currentT = 0.0;
float currentH = 0.0;
float currentCahaya = 0.0;

// LOGGER UTILITY
namespace Logger {
  void getTimestamp(char* buffer, size_t size) {
    struct tm timeinfo;
    if (getLocalTime(&timeinfo, 0)) {
      snprintf(buffer, size, "%02d:%02d:%02d", timeinfo.tm_hour, timeinfo.tm_min, timeinfo.tm_sec);
    } else {
      unsigned long ms = millis();
      unsigned long secs = ms / 1000;
      unsigned long mins = secs / 60;
      snprintf(buffer, size, "%02lu:%02lu.%03lu", mins % 60, secs % 60, ms % 1000);
    }
  }

  void log(const char* level, const char* format, va_list args) {
    char timeStr[16];
    getTimestamp(timeStr, sizeof(timeStr));
    
    char msgBuffer[256];
    vsnprintf(msgBuffer, sizeof(msgBuffer), format, args);

    Serial.printf("[%s] [%s] %s\n", timeStr, level, msgBuffer);
  }

  void info(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("INFO", format, args);
    va_end(args);
  }

  void success(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("OK", format, args);
    va_end(args);
  }

  void warn(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("WARN", format, args);
    va_end(args);
  }

  void error(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("ERROR", format, args);
    va_end(args);
  }

  void system(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("SYSTEM", format, args);
    va_end(args);
  }
}

void sendDataToWeb(String statusPompa);

// HELPER FUNCTIONS
int timeToMinutes(String timeStr) {
  if (timeStr.length() < 5)
    return -1;
  int h = timeStr.substring(0, 2).toInt();
  int m = timeStr.substring(3, 5).toInt();
  return (h * 60) + m;
}

bool checkScheduleActive() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo, 0)) {
    return false;
  }
  int currentMin = (timeinfo.tm_hour * 60) + timeinfo.tm_min;

  auto inRange = [](int current, int start, int end) -> bool {
    if (start == 0 && end == 0) return false;
    if (start <= end) {
      return current >= start && current <= end;
    } else {
      return current >= start || current <= end;
    }
  };

  bool isPagi = inRange(currentMin, pagi_mulai, pagi_selesai);
  bool isSiang = inRange(currentMin, siang_mulai, siang_selesai);
  bool isSore = inRange(currentMin, sore_mulai, sore_selesai);

  return isPagi || isSiang || isSore;
}

String updatePumpState() {
  String actualPumpStatus = "OFF";
  if (manualPumpStatus == "ON") {
    digitalWrite(RELAY_POMPA, RELAY_POMPA_ACTIVE_HIGH ? HIGH : LOW);
    actualPumpStatus = "ON";
  } else {
    // manualPumpStatus is "OFF" -> Normal Automatic Mode
    bool scheduleActive = checkScheduleActive();
    bool humidityLow = (currentH > 0.0 && currentH < batasKelembapanKering);
    
    if (scheduleActive || humidityLow) {
      digitalWrite(RELAY_POMPA, RELAY_POMPA_ACTIVE_HIGH ? HIGH : LOW);
      actualPumpStatus = "ON";
    } else {
      digitalWrite(RELAY_POMPA, RELAY_POMPA_ACTIVE_HIGH ? LOW : HIGH);
      actualPumpStatus = "OFF";
    }
  }
  return actualPumpStatus;
}

// API GET
void fetchScheduleFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    Logger::info("Fetching watering schedule parameters...");
    HTTPClient http;
    http.begin(secureClientGeneral, laravelEndpointSchedule);
    http.setTimeout(4000);

    int httpResponseCode = http.GET();
    if (httpResponseCode == 200) {
      String payload = http.getString();

      DynamicJsonDocument doc(1024);
      DeserializationError error = deserializeJson(doc, payload);
      if (error) {
        Logger::error("Schedule JSON parsing failed: %s", error.c_str());
        http.end();
        return;
      }

      if (String(doc["status"] | "") == "SUCCESS") {
        JsonObject data = doc["data"];
        pagi_mulai = timeToMinutes(data["pagi_mulai"].as<String>());
        pagi_selesai = timeToMinutes(data["pagi_selesai"].as<String>());
        siang_mulai = timeToMinutes(data["siang_mulai"].as<String>());
        siang_selesai = timeToMinutes(data["siang_selesai"].as<String>());
        sore_mulai = timeToMinutes(data["sore_mulai"].as<String>());
        sore_selesai = timeToMinutes(data["sore_selesai"].as<String>());
        
        if (data.containsKey("batas_kelembapan")) {
          batasKelembapanKering = data["batas_kelembapan"].as<float>();
        }

        Logger::success("Watering schedule updated. Pagi: %s-%s | Siang: %s-%s | Sore: %s-%s | Limit Lembap: %.1f%%",
                        data["pagi_mulai"].as<const char*>(), data["pagi_selesai"].as<const char*>(),
                        data["siang_mulai"].as<const char*>(), data["siang_selesai"].as<const char*>(),
                        data["sore_mulai"].as<const char*>(), data["sore_selesai"].as<const char*>(),
                        batasKelembapanKering);
      } else {
        Logger::warn("Server responded with status: %s", doc["status"] | "UNKNOWN");
      }
    } else {
      Logger::error("Failed to fetch schedule from server. HTTP Code: %d", httpResponseCode);
    }
    http.end();
  } else {
    Logger::warn("Fetch schedule skipped: WiFi disconnected.");
  }
}

// API GET PUMP
void fetchPumpStatusFromWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    if (!httpPump.connected()) {
      httpPump.begin(secureClientPump, laravelEndpointPumpStatus);
      httpPump.setReuse(true);
      httpPump.setTimeout(3000);
    }
    int httpResponseCode = httpPump.GET();

    if (httpResponseCode == 200) {
      String payload = httpPump.getString();

      DynamicJsonDocument doc(256);
      DeserializationError error = deserializeJson(doc, payload);
      if (error) {
        return;
      }

      if (doc.containsKey("status")) {
        String newStatus = doc["status"].as<String>();
        if (newStatus != manualPumpStatus) {
          manualPumpStatus = newStatus;
          Logger::info("PUMP MANUAL -> %s", manualPumpStatus.c_str());

          String actualPumpStatus = updatePumpState();

          sendDataToWeb(actualPumpStatus);
        }
      }
    } else {
      httpPump.end();
    }
  }
}

// API POST
void sendDataToWeb(String statusPompa) {
  if (WiFi.status() == WL_CONNECTED) {
    if (!httpTelemetry.connected()) {
      httpTelemetry.begin(secureClientTelemetry, laravelEndpointData);
      httpTelemetry.addHeader("Content-Type", "application/json");
      httpTelemetry.setReuse(true);
      httpTelemetry.setTimeout(4000);
    }

    String jsonPayload = "{";
    jsonPayload += "\"sensor_id\":\"JAMKOT-01\",";
    jsonPayload += "\"suhu\":" + String(currentT) + ",";
    jsonPayload += "\"kelembapan\":" + String(currentH) + ",";
    jsonPayload += "\"cahaya\":" + String(currentCahaya, 2) + ",";
    jsonPayload += "\"pompa_status\":\"" + statusPompa + "\"";
    jsonPayload += "}";

    int httpResponseCode = httpTelemetry.POST(jsonPayload);

    if (httpResponseCode > 0) {
      Logger::success("Telemetry successfully uploaded. HTTP Code: %d", httpResponseCode);
    } else {
      Logger::error("Telemetry upload failed: %s", httpTelemetry.errorToString(httpResponseCode).c_str());
      httpTelemetry.end();
    }
  } else {
    Logger::warn("Telemetry upload skipped: WiFi disconnected.");
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
    http.begin(secureClientHeartbeat, laravelEndpointHeartbeat);
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(4000);

    float h = dht.readHumidity();
    float t = dht.readTemperature();
    if (isnan(h) || isnan(t)) {
      delay(150);
      h = dht.readHumidity();
      t = dht.readTemperature();
    }
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
      Logger::success("Heartbeat sent. Temp: %.1f C | RSSI: %d dBm | Code: %d", espTemp, WiFi.RSSI(), httpResponseCode);
    } else {
      Logger::error("Heartbeat send FAILED (%s)", http.errorToString(httpResponseCode).c_str());
    }
    http.end();
  } else {
    Logger::warn("Heartbeat skipped: WiFi disconnected.");
  }
}

// INITIALIZATION
void setup() {
  Serial.begin(115200);
  delay(2000);

  Serial.println();
  Serial.println("│  JAMUR AUTOMATION MONITORING & CONTROL OVER TELEMETRY  │");
  Serial.println();

  pinMode(RELAY_KIPAS, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);

  digitalWrite(RELAY_KIPAS, RELAY_KIPAS_ACTIVE_HIGH ? LOW : HIGH);
  digitalWrite(RELAY_POMPA, RELAY_POMPA_ACTIVE_HIGH ? LOW : HIGH);

  secureClientPump.setInsecure();
  secureClientTelemetry.setInsecure();
  secureClientGeneral.setInsecure();
  secureClientHeartbeat.setInsecure();

  dht.begin();
  pinMode(LDR_PIN, INPUT);

  // WiFi
  Logger::system("Connecting to SSID '%s'...", ssid);
  WiFi.begin(ssid, password);
  unsigned long wifiStart = millis();
  bool wifiConnected = true;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    if (millis() - wifiStart > 15000) {
      wifiConnected = false;
      break;
    }
  }

  if (wifiConnected) {
    Logger::success("Connected to WiFi network. IP Address: %s", WiFi.localIP().toString().c_str());

    // NTP Time Sync
    Logger::system("Syncing clock parameters via NTP...");
    configTime(gmtOffset_sec, daylightOffset_sec, "pool.ntp.org");
    struct tm timeinfo;
    unsigned long ntpStart = millis();
    bool ntpSynced = false;
    while (millis() - ntpStart < 8000) {
      if (getLocalTime(&timeinfo, 500)) {
        ntpSynced = true;
        break;
      }
    }
    
    if (ntpSynced) {
      Logger::success("Clock synchronized successfully!");
    } else {
      Logger::error("Clock sync timed out. Uptime logs will be used.");
    }

    // Load schedule
    fetchScheduleFromWeb();
  } else {
    Logger::error("Failed to connect to WiFi. Entering offline/fallback mode.");
  }

  // Pre-load sensor values
  delay(1000);
  float h = dht.readHumidity();
  float t = dht.readTemperature();
  if (isnan(h) || isnan(t)) {
    delay(150);
    h = dht.readHumidity();
    t = dht.readTemperature();
  }
  if (!isnan(h) && !isnan(t)) {
    currentH = h;
    currentT = t;
  } else {
    Logger::warn("Failed to read initial climate values!");
  }
  
  // LDR: average voltage reading
  {
    uint32_t totalMv = 0;
    for (int i = 0; i < LDR_SAMPLES; i++) {
      totalMv += analogReadMilliVolts(LDR_PIN);
      delayMicroseconds(50);
    }
    float vOutMv = (float)totalMv / LDR_SAMPLES;

    // Hitung resistansi LDR (LDR on low side, fixed resistor pull-up)
    float rLdr;
    if (vOutMv <= 0.1f) {
      rLdr = 0.0f;
    } else if (vOutMv >= LDR_V_MAX_MV) {
      rLdr = 10000000.0f; // sangat gelap
    } else {
      rLdr = LDR_R_FIXED * (vOutMv / (LDR_VCC_MV - vOutMv));
    }

    // Hitung lux: lux = 10 * (R10 / R_LDR)^(1/alpha)
    if (rLdr <= 0.0f) {
      currentCahaya = 100000.0f; // sangat terang
    } else {
      currentCahaya = 10.0f * pow(LDR_R10 / rLdr, 1.0f / LDR_ALPHA);
    }
  }

  // Send initial heartbeat
  sendHeartbeat();

  Logger::success("System setup complete. Loop engine started.");
  Serial.println("──────────────────────────────────────────────────────\n");
}

// MAIN LOOP
void loop() {
  unsigned long currentMillis = millis();

  // Sync schedules hourly
  if (currentMillis - lastScheduleUpdate >= scheduleUpdateInterval) {
    lastScheduleUpdate = currentMillis;
    fetchScheduleFromWeb();
  }

  // Query pump status
  if (currentMillis - lastPumpStatusUpdate >= pumpStatusUpdateInterval) {
    lastPumpStatusUpdate = currentMillis;
    fetchPumpStatusFromWeb();
  }

  // Send heartbeat periodically
  if (lastHeartbeat == 0 || currentMillis - lastHeartbeat >= heartbeatInterval) {
    lastHeartbeat = currentMillis;
    sendHeartbeat();
  }

  // Controls Logic
  // Fan control threshold
  if (currentT > batasSuhuPanas) {
    digitalWrite(RELAY_KIPAS, RELAY_KIPAS_ACTIVE_HIGH ? HIGH : LOW);
  } else {
    digitalWrite(RELAY_KIPAS, RELAY_KIPAS_ACTIVE_HIGH ? LOW : HIGH);
  }

  // Pump control
  String actualPumpStatus = updatePumpState();

  // Process Sensors & Telemetry transmission
  if (lastSensorRead == 0 || currentMillis - lastSensorRead >= sensorReadInterval) {
    lastSensorRead = currentMillis;

    float h = dht.readHumidity();
    float t = dht.readTemperature();
    if (isnan(h) || isnan(t)) {
      delay(150);
      h = dht.readHumidity();
      t = dht.readTemperature();
    }
    if (!isnan(h) && !isnan(t)) {
      currentH = h;
      currentT = t;
    } else {
      Logger::warn("Failed to update climate parameters!");
    }

    // LDR: average voltage reading
    {
      uint32_t totalMv = 0;
      for (int i = 0; i < LDR_SAMPLES; i++) {
        totalMv += analogReadMilliVolts(LDR_PIN);
        delayMicroseconds(50);
      }
      float vOutMv = (float)totalMv / LDR_SAMPLES;

      // Hitung resistansi LDR (LDR on low side, fixed resistor pull-up)
      float rLdr;
      if (vOutMv <= 0.1f) {
        rLdr = 0.0f;
      } else if (vOutMv >= LDR_V_MAX_MV) {
        rLdr = 10000000.0f;
      } else {
        rLdr = LDR_R_FIXED * (vOutMv / (LDR_VCC_MV - vOutMv));
      }

      // Hitung lux: lux = 10 * (R10 / R_LDR)^(1/alpha)
      float targetLux;
      if (rLdr <= 0.0f) {
        targetLux = 100000.0f;
      } else {
        targetLux = 10.0f * pow(LDR_R10 / rLdr, 1.0f / LDR_ALPHA);
      }

      // Exponential Moving Average filter for smooth transitions
      currentCahaya = (currentCahaya * 0.7f) + (targetLux * 0.3f);
    }

    String actualKipasStatus = (currentT > batasSuhuPanas) ? "NYALA" : "MATI";

    // Simple, clean single-line telemetry logging
    Logger::info("Suhu: %.1fC (Batas: %.1fC) | Lembap: %.1f%% (Batas: %.1f%%) | Cahaya: %.2f Lux | Kipas: %s | Pompa: %s (Manual: %s)",
                 currentT, batasSuhuPanas, currentH, batasKelembapanKering, currentCahaya,
                 actualKipasStatus.c_str(), actualPumpStatus.c_str(), manualPumpStatus.c_str());

    sendDataToWeb(actualPumpStatus);
    Serial.println();
  }

  delay(50);
}