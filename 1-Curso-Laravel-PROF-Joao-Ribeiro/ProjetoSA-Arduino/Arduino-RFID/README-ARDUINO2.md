# 📡 SmartLOG - Montagem ESP32 com RFID

## 🎯 Componentes Necessários

| Quantidade | Componente | Descrição |
|------------|-----------|-----------|
| 1 | ESP32 DevKit | Microcontrolador principal |
| 3 | MFRC522 | Módulos leitores RFID |
| 1 | HC-SR04 | Sensor ultrassônico de distância |
| 1 | Buzzer Ativo 5V | Buzzer para alertas sonoros |
| 1 | Protoboard 830 pontos | Para montagem |
| N | Jumpers macho-macho | Para conexões |
| 1 | Fonte 5V | Alimentação (ou via USB) |

---

## 🔌 Diagrama de Pinos - ESP32

### 📊 Tabela Completa de Conexões

| Componente | Pino Componente | → | Pino ESP32 | Descrição |
|-----------|----------------|---|-----------|-----------|
| **LEITOR RFID 1 (Entrada)** |
| MFRC522 #1 | SDA (SS) | → | GPIO 21 | Chip Select |
| MFRC522 #1 | SCK | → | GPIO 18 | Clock SPI |
| MFRC522 #1 | MOSI | → | GPIO 23 | Master Out Slave In |
| MFRC522 #1 | MISO | → | GPIO 19 | Master In Slave Out |
| MFRC522 #1 | IRQ | → | Não conectar | - |
| MFRC522 #1 | GND | → | GND | Terra |
| MFRC522 #1 | RST | → | GPIO 22 | Reset (compartilhado) |
| MFRC522 #1 | 3.3V | → | 3.3V | Alimentação |
| **LEITOR RFID 2 (Produção)** |
| MFRC522 #2 | SDA (SS) | → | GPIO 17 | Chip Select |
| MFRC522 #2 | SCK | → | GPIO 18 | Clock SPI (compartilhado) |
| MFRC522 #2 | MOSI | → | GPIO 23 | MOSI (compartilhado) |
| MFRC522 #2 | MISO | → | GPIO 19 | MISO (compartilhado) |
| MFRC522 #2 | IRQ | → | Não conectar | - |
| MFRC522 #2 | GND | → | GND | Terra |
| MFRC522 #2 | RST | → | GPIO 22 | Reset (compartilhado) |
| MFRC522 #2 | 3.3V | → | 3.3V | Alimentação |
| **LEITOR RFID 3 (Expedição)** |
| MFRC522 #3 | SDA (SS) | → | GPIO 16 | Chip Select |
| MFRC522 #3 | SCK | → | GPIO 18 | Clock SPI (compartilhado) |
| MFRC522 #3 | MOSI | → | GPIO 23 | MOSI (compartilhado) |
| MFRC522 #3 | MISO | → | GPIO 19 | MISO (compartilhado) |
| MFRC522 #3 | IRQ | → | Não conectar | - |
| MFRC522 #3 | GND | → | GND | Terra |
| MFRC522 #3 | RST | → | GPIO 22 | Reset (compartilhado) |
| MFRC522 #3 | 3.3V | → | 3.3V | Alimentação |
| **SENSOR ULTRASSÔNICO** |
| HC-SR04 | VCC | → | 5V | Alimentação |
| HC-SR04 | TRIG | → | GPIO 25 | Trigger |
| HC-SR04 | ECHO | → | GPIO 26 | Echo |
| HC-SR04 | GND | → | GND | Terra |
| **BUZZER** |
| Buzzer Ativo | + (Positivo) | → | GPIO 27 | Sinal |
| Buzzer Ativo | - (Negativo) | → | GND | Terra |

---

## 🔧 Resumo dos Pinos ESP32 Utilizados

```
ESP32 PINOUT:
┌─────────────────────────┐
│  3.3V ──→ RFID 1,2,3   │  Alimentação 3.3V
│  5V   ──→ Ultrassônico  │  Alimentação 5V
│  GND  ──→ Todos         │  Terra comum
│                         │
│  GPIO 16 ──→ RFID 3 SS  │  Chip Select 3
│  GPIO 17 ──→ RFID 2 SS  │  Chip Select 2
│  GPIO 18 ──→ SCK (SPI)  │  Clock (todos RFID)
│  GPIO 19 ──→ MISO (SPI) │  MISO (todos RFID)
│  GPIO 21 ──→ RFID 1 SS  │  Chip Select 1
│  GPIO 22 ──→ RST (RFID) │  Reset (todos RFID)
│  GPIO 23 ──→ MOSI (SPI) │  MOSI (todos RFID)
│                         │
│  GPIO 25 ──→ TRIG       │  Ultrassônico Trigger
│  GPIO 26 ──→ ECHO       │  Ultrassônico Echo
│                         │
│  GPIO 27 ──→ Buzzer     │  Buzzer Ativo
└─────────────────────────┘
```

---

## 📐 Esquema de Montagem

### Passo 1: Conexões SPI (Compartilhadas)

**IMPORTANTE:** Os 3 leitores RFID compartilham os pinos SPI:

```
ESP32          Todos os 3 RFID
─────          ───────────────
GPIO 18  ──┬──→ SCK
GPIO 19  ──┼──→ MISO  
GPIO 23  ──┼──→ MOSI
GPIO 22  ──┴──→ RST
```

### Passo 2: Chip Select Individual

Cada leitor tem seu próprio pino SS (Chip Select):

```
ESP32          RFID
─────          ────
GPIO 21  ────→ RFID 1 (Entrada)
GPIO 17  ────→ RFID 2 (Produção)
GPIO 16  ────→ RFID 3 (Expedição)
```

### Passo 3: Alimentação

```
ESP32          Componente
─────          ──────────
3.3V     ────→ RFID 1, 2, 3 (VCC)
5V       ────→ HC-SR04 (VCC)
GND      ────→ Todos (GND)
```

---

## ⚙️ Configuração do Código

### 1. Instalar Bibliotecas no Arduino IDE

Vá em **Sketch → Include Library → Manage Libraries** e instale:

- ✅ **MFRC522** (by GithubCommunity) - versão 1.4.x
- ✅ **ArduinoJson** (by Benoit Blanchon) - versão 6.x
- ✅ **WiFi** (já incluída no ESP32)
- ✅ **HTTPClient** (já incluída no ESP32)

### 2. Configurar WiFi e Servidor

Edite estas linhas no código:

```cpp
const char* ssid = "SEU_WIFI";              // Nome da sua rede WiFi
const char* password = "SUA_SENHA";         // Senha do WiFi
const char* serverUrl = "http://SEU_IP:8080/api/rfid/reading";  // IP do servidor
```

### 3. Cadastrar Tags RFID

Primeiro, rode o código e aproxime suas tags para ver o ID:

```cpp
String tagsCadastradas[] = {
  "1a2b3c4d",  // Substitua pelos IDs reais
  "5e6f7g8h",
  "9i0j1k2l",
};
```

---

## 🚀 Upload e Teste

### 1. Selecionar Placa

No Arduino IDE:
- **Tools → Board → ESP32 Arduino → ESP32 Dev Module**

### 2. Selecionar Porta

- **Tools → Port → COM X** (Windows) ou **/dev/ttyUSB0** (Linux)

### 3. Fazer Upload

- Pressione **Upload** (Ctrl+U)
- Se der erro, segure o botão **BOOT** no ESP32

### 4. Abrir Serial Monitor

- **Tools → Serial Monitor**
- Configurar para **115200 baud**

---

## 🧪 Testando o Sistema

### Teste 1: Verificar Inicialização

Você deve ver:

```
╔════════════════════════════════════════╗
║   SmartLOG - Sistema RFID ESP32       ║
║   3 Leitores + Ultrassônico + Buzzer  ║
╚════════════════════════════════════════╝

✓ Leitores RFID inicializados
  - Leitor 1: Entrada Principal
  - Leitor 2: Setor de Produção
  - Leitor 3: Expedição/Saída

📡 Conectando ao WiFi: SEU_WIFI
✓ WiFi conectado!
   IP: 192.168.1.100
   Sinal: -52 dBm

✓ Sistema pronto!
```

### Teste 2: Testar Componentes

Digite **T** no Serial Monitor:

```
🧪 Testando componentes...

1. Testando Buzzer...
   ✓ Buzzer OK

2. Testando Sensor Ultrassônico...
   Distância: 25 cm
   Distância: 24 cm
   Distância: 26 cm
   ✓ Ultrassônico OK

3. Testando Leitores RFID...
   Aproxime uma tag de cada leitor...
   ✓ Leitores inicializados

✓ Teste concluído!
```

### Teste 3: Ler Tag RFID

Aproxime uma tag de qualquer leitor:

```
═══════════════════════════════════════
🏷️  TAG DETECTADA!
═══════════════════════════════════════
Tag ID: 1a2b3c4d
Setor: Entrada Principal
Status: ✓ TAG CADASTRADA

📤 Enviando para servidor...
{"tag_id":"1a2b3c4d","reader_id":"READER_ENTRADA",...}
✓ HTTP 201
✓ Dados enviados com sucesso!
═══════════════════════════════════════
```

---

## 🔊 Funcionamento do Buzzer

### Alertas de Proximidade (Ultrassônico)

| Distância | Som | Descrição |
|-----------|-----|-----------|
| < 10 cm | Bip contínuo (2000 Hz) | **MUITO PERTO** |
| 10-20 cm | Bips rápidos (1500 Hz) | Perto |
| 20-40 cm | Bips médios (1000 Hz) | Médio |
| 40-60 cm | Bips lentos (800 Hz) | Longe |
| > 60 cm | Sem som | Muito longe |

### Alertas de Leitura RFID

| Situação | Som | Descrição |
|----------|-----|-----------|
| Tag cadastrada | 2 bips curtos (1500 Hz) | Acesso permitido ✓ |
| Tag não cadastrada | 1 bip longo (500 Hz) | Acesso negado ✗ |

---

## 🎯 Setores e Status

| Leitor | Setor | Status Enviado | Reader ID |
|--------|-------|---------------|-----------|
| RFID 1 | Entrada Principal | `entrada` | READER_ENTRADA |
| RFID 2 | Setor de Produção | `movimentacao` | READER_PRODUCAO |
| RFID 3 | Expedição/Saída | `saida` | READER_EXPEDICAO |

---

## ⚠️ Solução de Problemas

### Problema: RFID não lê tags

**Soluções:**
1. ✅ Verifique se as tags estão a menos de 3cm do leitor
2. ✅ Confirme alimentação 3.3V (NÃO use 5V!)
3. ✅ Verifique conexões SPI (SCK, MISO, MOSI)
4. ✅ Teste com comando **T** no Serial Monitor

### Problema: Ultrassônico não funciona

**Soluções:**
1. ✅ Verifique alimentação 5V
2. ✅ Confirme pinos TRIG (GPIO 25) e ECHO (GPIO 26)
3. ✅ Não obstrua o sensor
4. ✅ Teste com comando **T**

### Problema: Buzzer não toca

**Soluções:**
1. ✅ Verifique se é buzzer **ATIVO** (não passivo)
2. ✅ Confirme pino GPIO 27
3. ✅ Teste invertendo polaridade
4. ✅ Teste com comando **T**

### Problema: WiFi não conecta

**Soluções:**
1. ✅ Verifique SSID e senha
2. ✅ Certifique-se que é rede 2.4GHz
3. ✅ Aproxime ESP32 do roteador
4. ✅ Verifique IP do servidor

### Problema: Servidor retorna erro 419

**Soluções:**
1. ✅ Verifique se CSRF está desabilitado para `/api/*`
2. ✅ Confirme que a rota existe
3. ✅ Teste com Postman primeiro

---

## 📸 Checklist Antes da Apresentação

- [ ] ✅ Todas as conexões firmes
- [ ] ✅ ESP32 ligado e conectado ao WiFi
- [ ] ✅ Serial Monitor aberto (115200 baud)
- [ ] ✅ Servidor Laravel rodando
- [ ] ✅ Dashboard aberto no navegador
- [ ] ✅ Tags RFID cadastradas
- [ ] ✅ Buzzer funcionando
- [ ] ✅ Ultrassônico respondendo
- [ ] ✅ Teste completo realizado

---

## 🎓 Comandos do Serial Monitor

| Comando | Função |
|---------|--------|
| **I** | Exibir informações do sistema |
| **T** | Testar todos os componentes |

---

## 🚀 Pronto para Apresentar!

Seu sistema está completo e funcional:
- ✅ 3 leitores RFID funcionando
- ✅ Sensor ultrassônico com alertas
- ✅ Buzzer com sons diferentes
- ✅ Dados sendo enviados para Laravel
- ✅ Dashboard mostrando tudo em tempo real

# 🚀 SmartLOG - Guia FÁCIL ESP32-C6

## 🎯 CONFIGURAÇÃO EM 5 MINUTOS

### 1️⃣ Instalar Arduino IDE

- Baixe: https://www.arduino.cc/en/software
- Instale normalmente

### 2️⃣ Adicionar ESP32-C6

No Arduino IDE:
1. **File → Preferences**
2. Em "Additional Board Manager URLs" cole:
   ```
   https://espressif.github.io/arduino-esp32/package_esp32_index.json
   ```
3. **Tools → Board → Boards Manager**
4. Procure "esp32"
5. Instale "esp32 by Espressif Systems"

### 3️⃣ Instalar Bibliotecas

**Tools → Manage Libraries**, procure e instale:
- ✅ **MFRC522** (by GithubCommunity)
- ✅ **ArduinoJson** (by Benoit Blanchon) - versão 6.x

### 4️⃣ Configurar o Código

Abra `smartlog_esp32_c6.ino` e mude **APENAS 3 LINHAS**:

```cpp
const char* ssid = "SEU_WIFI";              // 👈 Nome do WiFi
const char* password = "SUA_SENHA";         // 👈 Senha
const char* serverUrl = "http://192.168.1.100:8080/api/rfid/reading";  // 👈 IP do servidor
```

### 5️⃣ Upload

1. Conecte ESP32-C6 no USB
2. **Tools → Board → ESP32C6 Dev Module**
3. **Tools → Port → COM X** (escolha a porta)
4. Clique em **Upload** (seta →)
5. Aguarde "Done uploading"

### 6️⃣ Testar

1. **Tools → Serial Monitor**
2. Configure **115200 baud**
3. Você deve ver:
   ```
   ╔════════════════════════════════════════╗
   ║   SmartLOG - Sistema RFID ESP32-C6    ║
   ╚════════════════════════════════════════╝
   
   ✓ WiFi conectado!
   ✅ Sistema pronto! Aproxime uma tag...
   ```

---

## 🧪 TESTES RÁPIDOS

### Teste 1: Buzzer
Se ouviu 2 bips no início = ✅ Buzzer OK!

### Teste 2: WiFi
Se conectou e mostrou o IP = ✅ WiFi OK!

### Teste 3: RFID
Aproxime uma tag de qualquer leitor:
```
🏷️  TAG DETECTADA!
Tag ID: a1b2c3d4
Setor: Entrada Principal
✓ Dados enviados com sucesso!
```

### Teste 4: Ultrassônico
Aproxime a mão = Deve apitar! 🔊

---

## 📸 CHECKLIST APRESENTAÇÃO

- [ ] ✅ Todas conexões firmes
- [ ] ✅ ESP32-C6 ligado via USB
- [ ] ✅ Serial Monitor aberto
- [ ] ✅ WiFi conectado (veja IP no Serial)
- [ ] ✅ Servidor Laravel rodando
- [ ] ✅ Dashboard aberto no navegador
- [ ] ✅ 3 tags funcionando
- [ ] ✅ Buzzer apitando
- [ ] ✅ Ultrassônico detectando
- [ ] ✅ Bateria do notebook carregada!

---

## ⚠️ PROBLEMAS COMUNS

### "Não compila"
- ✅ Instalou ESP32 no Board Manager?
- ✅ Instalou bibliotecas MFRC522 e ArduinoJson?
- ✅ Selecionou "ESP32C6 Dev Module"?

### "RFID não lê"
- ✅ Tag a menos de 3cm do leitor?
- ✅ 3.3V conectado nos RFIDs?
- ✅ Testou cada leitor individualmente?

### "WiFi não conecta"
- ✅ Nome e senha corretos?
- ✅ Rede é 2.4GHz (não 5GHz)?
- ✅ ESP32-C6 perto do roteador?

### "Erro 419 no servidor"
- ✅ CSRF desabilitado para `/api/*`?
- ✅ Servidor Laravel rodando?
- ✅ IP do servidor correto?

---

## 🎓 DICAS PARA APRESENTAÇÃO

1. **Teste 1 DIA ANTES** - Monte tudo e deixe rodando 10 minutos
2. **Leve tags extras** - Pelo menos 5 tags
3. **Anote o IP** - Cole um post-it no notebook
4. **Carregue tudo** - Notebook, celular (hotspot backup)
5. **Chegue cedo** - Monte com calma antes
6. **Tenha um plano B** - Se der erro, mostre o dashboard com dados antigos

---

## 🎉 PRONTO!

Seu sistema está **100% funcional**!

- ✅ 3 leitores RFID
- ✅ Sensor ultrassônico
- ✅ Buzzer com alertas
- ✅ WiFi enviando dados
- ✅ Dashboard mostrando tudo

**BOA SORTE NA APRESENTAÇÃO! 🚀🔥**