#include <DHT.h>
#include <HTTPClient.h>
#include <WiFi.h>

// CONNECT TO YOUR WIFI
const char *ssid = "YOUR_SSID"; // GANTI DENGAN SSID DAN PASSWORD WIFI ANDA
const char *password =
    "YOUR_PASSWORD"; // GANTI DENGAN SSID DAN PASSWORD WIFI ANDA

// REST API
const char *laravelEndpoint =
    "REST_API_ENDPOINT"; // GANTI DENGAN URL BACKEND LARAVEL ANDA

// PIN DEFINITIONS
#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

#define LDR_PIN 34
#define RELAY_KIPAS 26
#define RELAY_POMPA 27

// MAX/MIN THRESHOLDS
const float BATAS_SUHU_PANAS = 28.0;
const float BATAS_KELEMBAPAN_KERING = 85.0;

void setup() {
  Serial.begin(115200);
  Serial.println("\n JAMKOT READY ");

  // SETUP RELAY
  pinMode(RELAY_KIPAS, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);
  digitalWrite(RELAY_KIPAS, HIGH); // FAN OFF
  digitalWrite(RELAY_POMPA, HIGH); // PUMP OFF

  // SETUP SENSOR
  dht.begin();
  pinMode(LDR_PIN, INPUT);

  // WIFI CONNECTION
  WiFi.begin(ssid, password);
  Serial.print("Konek ke WiFi.");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\n[SUKSES] Terhubung ke WiFi!");
  Serial.print("IP ESP32: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  delay(60000); // 1 MINUTE INTERVAL

  // READ SENSOR DATA
  float h = dht.readHumidity();
  float t = dht.readTemperature();
  int ldrRaw = analogRead(LDR_PIN); // THRESHOLD 0 - 4095

  if (isnan(h) || isnan(t)) {
    Serial.println("[ERROR] CANT READ DHT SENSOR!");
    return;
  }

  int persentaseCahaya = (ldrRaw / 4095.0) * 100; // LOG SENSOR DATA
  // int persentaseCahaya = 100 - ((ldrRaw / 4095.0) * 100);

  Serial.println("====================================");
  Serial.printf("Suhu: %.1f°C | Kelembapan: %.1f%% | Cahaya: %d\n", t, h,
                ldrValue);

  // RELAY CONTROL LOGIC
  String statusPompa = "OFF";
  String statusKipas = "OFF";

  // FAN LOGIC
  if (t > BATAS_SUHU_PANAS) {
    digitalWrite(RELAY_KIPAS, LOW);
    statusKipas = "ON";
    Serial.println(">> KIPAS ON (Kepanasan)");
  } else {
    digitalWrite(RELAY_KIPAS, HIGH);
  }

  // PUMP LOGIC
  if (h < BATAS_KELEMBAPAN_KERING) {
    digitalWrite(RELAY_POMPA, LOW);
    statusPompa = "ON";
    Serial.println(">> POMPA ON (Kekeringan)");
  } else {
    digitalWrite(RELAY_POMPA, HIGH);
  }

  // SEND DATA TO LARAVEL BACKEND
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(laravelEndpoint);
    http.addHeader("Content-Type", "application/json");

    // JSON PAYLOAD
    String jsonPayload = "{";
    jsonPayload += "\"suhu\":" + String(t) + ",";
    jsonPayload += "\"kelembapan\":" + String(h) + ",";
    jsonPayload += "\"cahaya\":" + String(persentaseCahaya) + ",";
    jsonPayload += "\"pompa_status\":\"" + statusPompa + "\"";
    jsonPayload += "}";

    Serial.println("SEND PAYLOAD: " + jsonPayload);

    // POST REQUEST
    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
      Serial.printf("[SUCCESS] Server merespon dengan kode: %d\n",
                    httpResponseCode);
    } else {
      Serial.printf("[FAILED] Error ngirim HTTP POST: %s\n",
                    http.errorToString(httpResponseCode).c_str());
    }
    http.end();
  } else {
    Serial.println("[ERROR] WIFI DISCONNECTED");
  }
}