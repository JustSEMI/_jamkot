#include <DHT.h>
#include <HTTPClient.h>
#include <WiFi.h>

// CONNECT TO YOUR WIFI
const char *ssid = "YOUR_SSID";
const char *password =
    "YOUR_PASSWORD";

// REST API
const char *laravelEndpoint =
    "REST_API_ENDPOINT";

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

  pinMode(RELAY_KIPAS, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);
  digitalWrite(RELAY_KIPAS, HIGH);
  digitalWrite(RELAY_POMPA, HIGH);

  dht.begin();
  pinMode(LDR_PIN, INPUT);

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
  delay(60000);

  float h = dht.readHumidity();
  float t = dht.readTemperature();
  int ldrRaw = analogRead(LDR_PIN);

  if (isnan(h) || isnan(t)) {
    Serial.println("[ERROR] CANT READ DHT SENSOR!");
    return;
  }

  int persentaseCahaya = (ldrRaw / 4095.0) * 100;

  Serial.println("====================================");
  Serial.printf("Suhu: %.1f°C | Kelembapan: %.1f%% | Cahaya: %d\n", t, h,
                ldrValue);

  String statusPompa = "OFF";
  String statusKipas = "OFF";

  if (t > BATAS_SUHU_PANAS) {
    digitalWrite(RELAY_KIPAS, LOW);
    statusKipas = "ON";
    Serial.println(">> KIPAS ON (Kepanasan)");
  } else {
    digitalWrite(RELAY_KIPAS, HIGH);
  }

  if (h < BATAS_KELEMBAPAN_KERING) {
    digitalWrite(RELAY_POMPA, LOW);
    statusPompa = "ON";
    Serial.println(">> POMPA ON (Kekeringan)");
  } else {
    digitalWrite(RELAY_POMPA, HIGH);
  }

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(laravelEndpoint);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{";
    jsonPayload += "\"suhu\":" + String(t) + ",";
    jsonPayload += "\"kelembapan\":" + String(h) + ",";
    jsonPayload += "\"cahaya\":" + String(persentaseCahaya) + ",";
    jsonPayload += "\"pompa_status\":\"" + statusPompa + "\"";
    jsonPayload += "}";

    Serial.println("SEND PAYLOAD: " + jsonPayload);

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