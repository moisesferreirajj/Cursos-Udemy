# 🚀 SmartLOG - Guia FÁCIL ESP32-C6

## 📦 Lista de Materiais

| Qtd | Item | Preço Aprox. |
|-----|------|--------------|
| 1 | ESP32-C6 DevKit | R$ 25-40 |
| 3 | MFRC522 (leitor RFID) | R$ 15 (3x R$ 5) |
| 1 | HC-SR04 (ultrassônico) | R$ 5-8 |
| 1 | Buzzer Ativo 5V | R$ 2-3 |
| 3+ | Tags RFID | R$ 5 (pacote) |
| 1 | Protoboard 830 pontos | R$ 10-15 |
| 20+ | Jumpers macho-macho | R$ 8-12 |
| 1 | Cabo USB-C | Incluído |

**💰 Total:** ~R$ 70-100

---

## 🎨 DIAGRAMA VISUAL COLORIDO

```
╔══════════════════════════════════════════════════════════════╗
║                    VISTA SUPERIOR                            ║
╚══════════════════════════════════════════════════════════════╝

        [RFID 1]    [RFID 2]    [RFID 3]
           │           │           │
           │           │           │         [Ultrassônico]
           │           │           │              │
           └───────────┴───────────┴──────────────┤
                                                  │
        ┌──────────────────────────────────────────┴─┐
        │                                            │
        │              PROTOBOARD                    │
        │  ┌──────────────────────────────────────┐ │
        │  │         [ESP32-C6 DevKit]            │ │
        │  │                                      │ │
        │  │    USB-C aqui ──→  □                │ │
        │  └──────────────────────────────────────┘ │
        │                                            │
        │                 [Buzzer]                   │
        └────────────────────────────────────────────┘
```

---

## 🔌 TABELA DE CONEXÕES SIMPLIFICADA

### ⚡ Alimentação (Conecte PRIMEIRO!)

| ESP32-C6 | → | Componente |
|----------|---|------------|
| **3.3V** | → | RFID 1, 2, 3 → Pino VCC |
| **5V** | → | HC-SR04 → Pino VCC |
| **GND** | → | **TODOS** (RFID 1,2,3 + HC-SR04 + Buzzer) |

### 📡 RFID (3 leitores)

| ESP32-C6 | → | RFID 1 | RFID 2 | RFID 3 |
|----------|---|--------|--------|--------|
| GPIO 21 | → | SS ✓ | - | - |
| GPIO 17 | → | - | SS ✓ | - |
| GPIO 16 | → | - | - | SS ✓ |
| GPIO 18 | → | SCK (TODOS) | SCK ✓ | SCK ✓ |
| GPIO 19 | → | MISO (TODOS) | MISO ✓ | MISO ✓ |
| GPIO 23 | → | MOSI (TODOS) | MOSI ✓ | MOSI ✓ |
| GPIO 22 | → | RST (TODOS) | RST ✓ | RST ✓ |

### 📏 Sensor Ultrassônico

| ESP32-C6 | → | HC-SR04 |
|----------|---|---------|
| GPIO 25 | → | TRIG |
| GPIO 26 | → | ECHO |

### 🔊 Buzzer

| ESP32-C6 | → | Buzzer |
|----------|---|--------|
| GPIO 27 | → | + (positivo) |
| GND | → | - (negativo) |

---

## 📋 PASSO A PASSO (Não pule nenhum!)

### ✅ PASSO 1: Organize os Materiais

Coloque na mesa nesta ordem:
1. Protoboard
2. ESP32-C6
3. 3 RFIDs
4. HC-SR04
5. Buzzer
6. Jumpers coloridos (use cores diferentes!)

---

### ✅ PASSO 2: Encaixe o ESP32-C6 na Protoboard

```
┌─────────────────────────────┐
│       PROTOBOARD            │
│                             │
│   ┌─────────────────┐       │
│   │   ESP32-C6      │       │
│   │  [  USB-C  ]    │ ←── Encaixe no MEIO
│   │                 │       │
│   │  Pinos dos lados│       │
│   └─────────────────┘       │
│                             │
└─────────────────────────────┘
```

**⚠️ IMPORTANTE:** 
- Encaixe no **CENTRO** da protoboard
- Deixe espaço dos 2 lados para conectar jumpers
- Pressione firme até os pinos entrarem

---

### ✅ PASSO 3: Alimentação (FAÇA PRIMEIRO!)

#### 3.1 - GND Comum (PRETO)

Use jumpers PRETOS para GND:

```
ESP32 GND ──┬── RFID 1 GND
            ├── RFID 2 GND
            ├── RFID 3 GND
            ├── HC-SR04 GND
            └── Buzzer GND (-)
```

**Dica:** Use uma linha de GND na protoboard!

#### 3.2 - 3.3V (VERMELHO)

```
ESP32 3.3V ──┬── RFID 1 VCC
             ├── RFID 2 VCC
             └── RFID 3 VCC
```

#### 3.3 - 5V (LARANJA)

```
ESP32 5V ──→ HC-SR04 VCC
```

---

### ✅ PASSO 4: Conectar RFID 1 (Entrada)

| Cor Sugerida | ESP32-C6 | → | RFID 1 |
|--------------|----------|---|--------|
| 🟥 Vermelho | 3.3V | → | VCC |
| ⬛ Preto | GND | → | GND |
| 🟦 Azul | GPIO 21 | → | SDA/SS |
| 🟩 Verde | GPIO 18 | → | SCK |
| 🟨 Amarelo | GPIO 23 | → | MOSI |
| 🟧 Laranja | GPIO 19 | → | MISO |
| 🟪 Roxo | GPIO 22 | → | RST |
| - | - | - | IRQ (não conecte) |

---

### ✅ PASSO 5: Conectar RFID 2 (Produção)

| Cor Sugerida | ESP32-C6 | → | RFID 2 |
|--------------|----------|---|--------|
| 🟥 Vermelho | 3.3V | → | VCC |
| ⬛ Preto | GND | → | GND |
| 🔵 Azul Claro | GPIO 17 | → | SDA/SS |
| 🟩 Verde | GPIO 18 | → | SCK (compartilhado) |
| 🟨 Amarelo | GPIO 23 | → | MOSI (compartilhado) |
| 🟧 Laranja | GPIO 19 | → | MISO (compartilhado) |
| 🟪 Roxo | GPIO 22 | → | RST (compartilhado) |

---

### ✅ PASSO 6: Conectar RFID 3 (Expedição)

| Cor Sugerida | ESP32-C6 | → | RFID 3 |
|--------------|----------|---|--------|
| 🟥 Vermelho | 3.3V | → | VCC |
| ⬛ Preto | GND | → | GND |
| 🟦 Azul Escuro | GPIO 16 | → | SDA/SS |
| 🟩 Verde | GPIO 18 | → | SCK (compartilhado) |
| 🟨 Amarelo | GPIO 23 | → | MOSI (compartilhado) |
| 🟧 Laranja | GPIO 19 | → | MISO (compartilhado) |
| 🟪 Roxo | GPIO 22 | → | RST (compartilhado) |

---

### ✅ PASSO 7: Conectar Sensor Ultrassônico

| Cor Sugerida | ESP32-C6 | → | HC-SR04 |
|--------------|----------|---|---------|
| 🔴 Vermelho Escuro | 5V | → | VCC |
| ⬛ Preto | GND | → | GND |
| 🟢 Verde Claro | GPIO 25 | → | TRIG |
| 🟡 Amarelo Claro | GPIO 26 | → | ECHO |

---

### ✅ PASSO 8: Conectar Buzzer

| Cor Sugerida | ESP32-C6 | → | Buzzer |
|--------------|----------|---|--------|
| 🟣 Roxo Claro | GPIO 27 | → | + (positivo) |
| ⬛ Preto | GND | → | - (negativo) |

---

## 🔍 CHECKLIST ANTES DE LIGAR

Antes de conectar o USB, confira:

- [ ] ✅ ESP32-C6 firme na protoboard
- [ ] ✅ **TODOS os GNDs conectados** (CRÍTICO!)
- [ ] ✅ 3.3V nos 3 RFIDs
- [ ] ✅ 5V no HC-SR04
- [ ] ✅ Cada RFID tem seu próprio SS (GPIO 21, 17, 16)
- [ ] ✅ SCK, MISO, MOSI, RST compartilhados entre RFIDs
- [ ] ✅ TRIG e ECHO no HC-SR04
- [ ] ✅ Buzzer no GPIO 27
- [ ] ✅ Nenhum fio solto ou encostando onde não deve

---

## 💻 CÓDIGO PARA ESP32-C6

<function_calls>
<invoke name="artifacts">
<parameter name="command">update