<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Config;
use App\Container\Container;
use App\Http\ApiClient;
use App\BinProvider\BinProvider;
use App\RateProvider\RateProvider;
use App\CommissionCalculator;
use App\TransactionProcessor;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
    'BIN_LOOKUP_URL',
    'BIN_LOOKUP_API_KEY',
    'EXCHANGE_RATES_URL',
    'EXCHANGE_RATES_API_KEY',
    'EU_COUNTRIES',
    'EU_COMMISSION_RATE',
    'NON_EU_COMMISSION_RATE',
]);

// Load config
$configArray = require __DIR__ . '/config.php';
$config = new Config($configArray);

// Set up container
$container = new Container();

// Cache
$container->set('cache.exchange_rates', fn () =>
new FilesystemAdapter(namespace: 'exchange_rates_cache')
);

// HTTP client
$container->set('http.client', fn () => new Client());

// API client
$container->set('http.api_client', fn (Container $c) =>
new ApiClient(client: $c->get('http.client'))
);

// Bin provider
$container->set('bin_provider', fn (Container $c) =>
new BinProvider(
    url: $config->get('bin_lookup')['url'],
    apiKey: $config->get('bin_lookup')['api_key'],
    client: $c->get('http.api_client')
)
);

// Exchange rate provider

$container->set('exchange_rate_provider', fn (Container $c) =>
new RateProvider(
    url: $config->get('exchange_rates')['url'],
    apiKey: $config->get('exchange_rates')['api_key'],
    client: $c->get('http.api_client'),
    cache: $c->get('cache.exchange_rates')
)
);

// Commission calculator
$container->set('commission_calculator', fn (Container $c) =>
new CommissionCalculator(
    binProvider: $c->get('bin_provider'),
    exchangeRateProvider: $c->get('exchange_rate_provider'),
    euCountries: explode(',', $_ENV['EU_COUNTRIES']),
    euCommissionRate: (float) $_ENV['EU_COMMISSION_RATE'],
    nonEuCommissionRate: (float) $_ENV['NON_EU_COMMISSION_RATE'],
    currencyDecimals: $config->get('currency_decimals', [])
)
);

// Transaction processor
$container->set('transaction_processor', fn (Container $c) =>
new TransactionProcessor(
    calculator: $c->get('commission_calculator')
)
);

// CLI input
if ($argc < 2) {
    echo "Usage: php index.php <input_file>\n";
    exit(1);
}

// Process transactions
$processor = $container->get('transaction_processor');
$processor->processFile($argv[1]);
