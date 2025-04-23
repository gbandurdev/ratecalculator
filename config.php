<?php

use App\Config\Env;

return [
    'bin_lookup' => [
        'url' => $_ENV[Env::BIN_LOOKUP_URL] ?? 'https://lookup.binlist.net/',
        'api_key' => $_ENV[Env::BIN_LOOKUP_API_KEY] ?? '',
    ],
    'exchange_rates' => [
        'url' => $_ENV[Env::EXCHANGE_RATES_URL] ?? 'https://api.apilayer.com/exchangerates_data/latest',
        'api_key' => $_ENV[Env::EXCHANGE_RATES_API_KEY] ?? '',
    ],
    'eu_countries' => explode(',', $_ENV[Env::EU_COUNTRIES] ?? ''),
    'eu_commission_rate' => (float) ($_ENV[Env::EU_COMMISSION_RATE] ?? 0.01),
    'non_eu_commission_rate' => (float) ($_ENV[Env::NON_EU_COMMISSION_RATE] ?? 0.02),
];