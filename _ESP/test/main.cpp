#include <Arduino.h>
#define LDR_PIN 34 
#define LDR_ON_HIGH_SIDE false


const float V_CC = 3300.0;
const float R_FIXED = 100000.0;
const float LDR_R10 = 100000.0;   
const float LDR_ALPHA = 0.6;     
const int NUM_SAMPLES = 64;      

float getAverageVoltage(int pin, int samples);
float calculateLdrResistance(float voltageMilliVolts);
float calculateLux(float resistanceOhms);

void setup() {
  Serial.begin(115200);
  while (!Serial) {
  }
  
  pinMode(LDR_PIN, INPUT);
  
  Serial.println("===============================================");
  Serial.println("ESP32 Accurate LDR to Lux Converter Initialized");
  Serial.printf("Configured Pin: GPIO %d\n", LDR_PIN);
  Serial.printf("Wiring Mode: %s\n", LDR_ON_HIGH_SIDE ? "LDR on High Side (Pull-Down)" : "LDR on Low Side (Pull-Up)");
  Serial.printf("Fixed Resistor: %.1f Ohm\n", R_FIXED);
  Serial.printf("LDR Characteristics: R10 = %.1f Ohm, Alpha = %.2f\n", LDR_R10, LDR_ALPHA);
  Serial.println("===============================================");
}

void loop() {
  float vOutMilliVolts = getAverageVoltage(LDR_PIN, NUM_SAMPLES);
  float rLdr = calculateLdrResistance(vOutMilliVolts);
  float lux = calculateLux(rLdr);
  
  Serial.print("V_out: ");
  Serial.print(vOutMilliVolts, 1);
  Serial.print(" mV | R_LDR: ");
  if (rLdr >= 1000000.0) {
    Serial.print(rLdr / 1000000.0, 2);
    Serial.print(" MOhm");
  } else {
    Serial.print(rLdr / 1000.0, 2);
    Serial.print(" kOhm");
  }
  Serial.print(" | Light: ");
  Serial.print(lux, 2);
  Serial.println(" Lux");
  
  delay(1000);
}

float getAverageVoltage(int pin, int samples) {
  uint32_t totalVoltage = 0;
  for (int i = 0; i < samples; i++) {
    totalVoltage += analogReadMilliVolts(pin);
    delayMicroseconds(50);
  }
  return (float)totalVoltage / samples;
}

float calculateLdrResistance(float voltageMilliVolts) {
  if (voltageMilliVolts <= 0.1) {
    if (LDR_ON_HIGH_SIDE) return 10000000.0; 
    else return 0.0;                         
  }
  
  const float V_MAX_ADC = 3100.0;
  if (voltageMilliVolts >= V_MAX_ADC) {
    if (LDR_ON_HIGH_SIDE) return 0.0;
    else return 10000000.0;
  }

  float resistance = 0.0;
  if (LDR_ON_HIGH_SIDE) {
    resistance = R_FIXED * ((V_CC / voltageMilliVolts) - 1.0);
  } else {
    resistance = R_FIXED * (voltageMilliVolts / (V_CC - voltageMilliVolts));
  }
  
  return resistance;
}

float calculateLux(float resistanceOhms) {
  if (resistanceOhms <= 0.0) {
    return 100000.0;
  }

  float base = LDR_R10 / resistanceOhms;
  float exponent = 1.0 / LDR_ALPHA;
  float lux = 10.0 * pow(base, exponent);
  
  return lux;
}
