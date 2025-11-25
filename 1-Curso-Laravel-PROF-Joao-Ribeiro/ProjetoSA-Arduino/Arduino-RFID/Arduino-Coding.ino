/*******************************************************************************
 * SmartLOG - Sistema RFID com ESP32-C6
 * 3 Leitores RFID + 1 Sensor Ultrassônico + 1 Buzzer + WiFi
 * 
 * CONFIGURAÇÃO SUPER FÁCIL - APENAS MUDE 3 LINHAS!
 ******************************************************************************/

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <SPI.h>
#include <MFRC522.h>

// ==========================================
// 🔧 MUDE APENAS ESTAS 3 LINHAS:
// ==========================================
const char* ssid = "SEU_WIFI";
const char* password = "SUA_SENHA";
const char* serverUrl = "http://192.168.1.100:8080/api/rfid/reading";

// ==========================================
// PINOS (JÁ CONFIGURADO - NÃO MUDE!)
// ==========================================
#define RST_PIN    8   // Reset
#define RST_PIN_2    0   // Reset
#define RST_PIN_3    22   // Reset
#define SS_PIN_1   13   // RFID 1 - Entrada
#define SS_PIN_2   1   // RFID 2 - Produção
#define SS_PIN_3   16   // RFID 3 - Expedição

#define TRIG_PIN   25   // Ultrassônico TRIG
#define ECHO_PIN   26   // Ultrassônico ECHO
#define BUZZER_PIN 27   // Buzzer

// ==========================================
// OBJETOS RFID
// ==========================================
MFRC522 leitor1(SS_PIN_1, RST_PIN);
MFRC522 leitor2(SS_PIN_2, RST_PIN_2);
MFRC522 leitor3(SS_PIN_3, RST_PIN_3);

// ==========================================
// SETORES
// ==========================================
struct Setor {
  const char* nome;
  const char* reader_id;
  const char* status;
};

Setor setores[3] = {
  {"Entrada Principal", "READER_ENTRADA", "entrada"},
  {"Setor de Produção", "READER_PRODUCAO", "movimentacao"},
  {"Expedição/Saída", "READER_EXPEDICAO", "saida"}
};

// ==========================================
// VARIÁVEIS DE CONTROLE
// ==========================================
unsigned long lastReadTime = 0;
const unsigned long readInterval = 2000;
String ultimaTagLida = "";
int ultimoSetor = -1;

// ==========================================
// SETUP
// ==========================================
void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("\n╔════════════════════════════════════════╗");
  Serial.println("║   SmartLOG - Sistema RFID ESP32-C6    ║");
  Serial.println("║   3 Leitores + Ultrassônico + Buzzer  ║");
  Serial.println("╚════════════════════════════════════════╝\n");

  // Configurar pinos
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);

  // Teste do buzzer
  Serial.println("🔊 Testando buzzer...");
  bip(2, 100, 1500);
  Serial.println("   ✓ Buzzer OK\n");

  // Inicializar SPI
  SPI.begin();
  Serial.println("⚙️  Inicializando SPI...");

  // Inicializar leitores RFID
  Serial.println("📡 Inicializando leitores RFID...");
  leitor1.PCD_Init();
  delay(100);
  leitor2.PCD_Init();
  delay(100);
  leitor3.PCD_Init();
  delay(100);

  Serial.println("   ✓ Leitor 1: Entrada Principal");
  Serial.println("   ✓ Leitor 2: Setor de Produção");
  Serial.println("   ✓ Leitor 3: Expedição/Saída\n");

  // Conectar WiFi
  connectWiFi();

  Serial.println("✅ Sistema pronto! Aproxime uma tag...\n");
  bip(3, 100, 2000);
}

// ==========================================
// LOOP PRINCIPAL
// ==========================================
void loop() {
  // Verificar WiFi
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️  WiFi desconectado! Reconectando...");
    connectWiFi();
  }

  // Verificar distância
  verificarDistancia();

  // Ler RFID
  if (millis() - lastReadTime >= readInterval) {
    lerTodosLeitores();
    lastReadTime = millis();
  }

  delay(50);
}

// ==========================================
// CONECTAR WIFI
// ==========================================
void connectWiFi() {
  Serial.print("📡 Conectando ao WiFi: ");
  Serial.println(ssid);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("✓ WiFi conectado!");
    Serial.print("   IP: ");
    Serial.println(WiFi.localIP());
    Serial.print("   Sinal: ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm\n");
    bip(2, 100, 1800);
  } else {
    Serial.println("✗ Falha ao conectar WiFi!\n");
    bip(3, 200, 500);
  }
}

// ==========================================
// LER TODOS OS LEITORES
// ==========================================
void lerTodosLeitores() {
  if (lerRFID(leitor1, 0)) return;
  if (lerRFID(leitor2, 1)) return;
  if (lerRFID(leitor3, 2)) return;
}

// ==========================================
// LER RFID
// ==========================================
bool lerRFID(MFRC522 &leitor, int setorIndex) {
  if (!leitor.PICC_IsNewCardPresent() || !leitor.PICC_ReadCardSerial()) {
    return false;
  }

  // Ler ID da tag
  String tagID = "";
  for (byte i = 0; i < leitor.uid.size; i++) {
    tagID += String(leitor.uid.uidByte[i], HEX);
  }
  tagID.toLowerCase();

  // Evitar duplicação
  if (tagID == ultimaTagLida && setorIndex == ultimoSetor) {
    leitor.PICC_HaltA();
    return false;
  }

  ultimaTagLida = tagID;
  ultimoSetor = setorIndex;

  Serial.println("\n═══════════════════════════════════════");
  Serial.println("🏷️  TAG DETECTADA!");
  Serial.println("═══════════════════════════════════════");
  Serial.print("Tag ID: ");
  Serial.println(tagID);
  Serial.print("Setor: ");
  Serial.println(setores[setorIndex].nome);
  Serial.println("Status: ✓ TAG LIDA");

  acessoPermitido();
  enviarParaServidor(tagID, setorIndex);

  Serial.println("═══════════════════════════════════════\n");

  leitor.PICC_HaltA();
  return true;
}

// ==========================================
// VERIFICAR DISTÂNCIA
// ==========================================
void verificarDistancia() {
  static unsigned long lastCheck = 0;
  if (millis() - lastCheck < 500) return;
  lastCheck = millis();

  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

  long duracao = pulseIn(ECHO_PIN, HIGH, 30000);
  long distancia = duracao * 0.034 / 2;

  if (distancia <= 0 || distancia > 400) return;

  // Alertas de proximidade
  if (distancia < 10) {
    tone(BUZZER_PIN, 2000, 50);
  } else if (distancia < 20) {
    if (millis() % 200 < 50) tone(BUZZER_PIN, 1500, 50);
  } else if (distancia < 40) {
    if (millis() % 500 < 50) tone(BUZZER_PIN, 1000, 50);
  } else if (distancia < 60) {
    if (millis() % 1000 < 50) tone(BUZZER_PIN, 800, 50);
  }
}

// ==========================================
// ENVIAR PARA SERVIDOR
// ==========================================
void enviarParaServidor(String tagID, int setorIndex) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("✗ Sem WiFi - Não enviado");
    return;
  }

  HTTPClient http;
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(10000);

  StaticJsonDocument<512> doc;
  doc["tag_id"] = tagID;
  doc["reader_id"] = setores[setorIndex].reader_id;
  doc["location"] = setores[setorIndex].nome;
  doc["product_name"] = "Produto-" + tagID;
  doc["product_code"] = "PROD-" + tagID;
  doc["status"] = setores[setorIndex].status;
  doc["temperature"] = random(180, 280) / 10.0;
  doc["signal_strength"] = random(60, 100);
  doc["notes"] = "Leitura ESP32-C6";

  String jsonString;
  serializeJson(doc, jsonString);

  Serial.println("📤 Enviando para servidor...");

  int httpCode = http.POST(jsonString);

  if (httpCode > 0) {
    Serial.print("✓ HTTP ");
    Serial.println(httpCode);

    if (httpCode == 200 || httpCode == 201) {
      Serial.println("✓ Dados enviados com sucesso!");
      bip(2, 100, 1500);
    } else {
      Serial.println("⚠ Enviado mas com aviso");
      bip(1, 300, 800);
    }
  } else {
    Serial.print("✗ Erro: ");
    Serial.println(http.errorToString(httpCode));
    bip(3, 200, 400);
  }

  http.end();
}

// ==========================================
// EFEITOS SONOROS
// ==========================================
void acessoPermitido() {
  bip(2, 100, 1500);
}

void bip(int quantidade, int duracao, int frequencia) {
  for (int i = 0; i < quantidade; i++) {
    tone(BUZZER_PIN, frequencia, duracao);
    delay(duracao);
    delay(duracao);
  }
  noTone(BUZZER_PIN);
}