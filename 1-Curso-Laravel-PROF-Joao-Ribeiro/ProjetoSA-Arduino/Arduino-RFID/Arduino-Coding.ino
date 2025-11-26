#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <HTTPClient.h>

// ===========================
// RFID
// ===========================
#define SS_PIN    21
#define RST_PIN   22

MFRC522 rfid(SS_PIN, RST_PIN);

// ===========================
// LED + BUZZER
// ===========================
#define LED_VERDE 14
#define BUZZER    25

// ===========================
// WIFI
// ===========================
const char* ssid = "mark";
const char* password = "mark1234";

// ===========================
// API
// ===========================
String apiURL = "http://10.200.222.127:8080/api/rfid/reading";

// ===========================
// SIMULAÇÃO DE READERS
// ===========================
String readers[3] = { "READER_001", "READER_002", "READER_003" };
String tipos[3]   = { "entrada", "movimentacao", "saida" };
int readerIndex = 0;

// ===========================
// SETUP
// ===========================
void setup() {
  Serial.begin(115200);
  delay(300);

  pinMode(LED_VERDE, OUTPUT);
  pinMode(BUZZER, OUTPUT);

  digitalWrite(LED_VERDE, LOW);
  digitalWrite(BUZZER, LOW);

  // RFID
  SPI.begin(18, 19, 23);
  rfid.PCD_Init();

  // WIFI
  WiFi.begin(ssid, password);
  Serial.print("Conectando WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("\n[OK] WiFi conectado!");
  Serial.println("IP: " + WiFi.localIP().toString());

  Serial.println("\n=== RFID ATIVO NO SDA 21 ===");
  Serial.println("Simulando READER_001 → READER_002 → READER_003");
  Serial.println("Aproxime uma TAG...\n");
}

// ===========================
// Função: Enviar leitura para API Laravel
// ===========================
void sendToAPI(String uid, String readerID, String status) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[ERRO] Sem WiFi!");
    return;
  }

  HTTPClient http;
  http.setTimeout(15000);
  http.setConnectTimeout(8000);
  
  http.begin(apiURL);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");

  String json = "{";
  json += "\"tag_id\":\"" + uid + "\",";
  json += "\"reader_id\":\"" + readerID + "\",";
  json += "\"status\":\"" + status + "\"";
  json += "}";

  Serial.println("===============================");
  Serial.println("Enviando para API:");
  Serial.println("JSON: " + json);
  Serial.println("-------------------------------");

  int httpCode = http.POST(json);

  if (httpCode > 0) {
    Serial.print("[OK] HTTP Code: ");
    Serial.println(httpCode);
    
    if (httpCode == 201) {
      String response = http.getString();
      Serial.println("[SUCESSO] Leitura registrada!");
      Serial.println("Resposta: " + response);
      
      // Feedback positivo
      digitalWrite(LED_VERDE, HIGH);
      tone(BUZZER, 2500, 100);
      delay(500);
      noTone(BUZZER);
      tone(BUZZER, 3000, 100);
      digitalWrite(LED_VERDE, LOW);
    }
    
  } else {
    Serial.println("[ERRO] Código: " + String(httpCode));
    tone(BUZZER, 500, 200);
  }

  Serial.println("===============================\n");
  http.end();
}
// ===========================
// LOOP
// ===========================
void loop() {

  if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {

    // UID sem espaços
    String uid = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
      if (rfid.uid.uidByte[i] < 0x10) uid += "0";
      uid += String(rfid.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();

    String readerName = readers[readerIndex];
    String status = tipos[readerIndex];

    Serial.println("\n*** TAG DETECTADA ***");
    Serial.print("Reader: ");
    Serial.println(readerName);
    Serial.print("TAG UID: ");
    Serial.println(uid);
    Serial.print("Status: ");
    Serial.println(status);

    // LED + buzzer (feedback imediato)
    digitalWrite(LED_VERDE, HIGH);
    tone(BUZZER, 2000, 120);
    delay(550);
    digitalWrite(LED_VERDE, LOW);

    // Aguarda um pouco antes de enviar
    delay(300);

    // Envia para API
    sendToAPI(uid, readerName, status);

    // Próximo leitor
    readerIndex++;
    if (readerIndex >= 3) readerIndex = 0;

    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    
    // Aguarda antes de permitir nova leitura
    delay(2000);
  }

  delay(80);
}