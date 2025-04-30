# Commission Calculator

A lightweight PHP application that calculates transaction commissions based on BIN, currency, and amount. It uses external providers to fetch BIN details and exchange rates and applies different commission rates depending on whether the transaction is from an EU country.

---

## 📦 Requirements

- PHP 8.3+
- Composer

---

## 🚀 Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/your-username/commission-calculator.git
   cd commission-calculator
   composer install
   cp .env.example .env

2. Create a .env file based on .env.example:
    ```bash
    BIN_LOOKUP_URL=https://lookup.binlist.net/
    BIN_LOOKUP_API_KEY=
    EXCHANGE_RATES_URL=https://api.apilayer.com/exchangerates_data/latest
    EXCHANGE_RATES_API_KEY=
    EU_COUNTRIES=AT,BE,BG,CY,CZ,DE,DK,EE,ES,FI,FR,GR,HR,HU,IE,IT,LT,LU,LV,MT,NL,PO,PT,RO,SE,SI,SK
    EU_COMMISSION_RATE=0.01
    NON_EU_COMMISSION_RATE=0.02
## 🧪 Tests

1. **Running Tests**

   Unit tests are written using PHPUnit.

    ```bash 
    vendor/bin/phpunit
   
## ⚙️️ Usage
1. **Running The Code**

   To calculate commissions from a file:

    ```bash 
    php run.php path/to/input.txt
   
2. **Each line in the input file should be a JSON object:**
    ```bash
    {"bin":"45717360","amount":"100.00","currency":"EUR"}
    {"bin":"516793","amount":"50.00","currency":"USD"}

## ⚙️️ Built With
- PHP 8.3+
- PHPUnit 12
- Composer
- Guzzle
- Symfony Cache
- vlucas/phpdotenv