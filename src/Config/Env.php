<?php

namespace App\Config;

/**
 * Centralized environment variable keys.
 */
final class Env
{
    public const string BIN_LOOKUP_URL         = 'BIN_LOOKUP_URL';
    public const string BIN_LOOKUP_API_KEY     = 'BIN_LOOKUP_API_KEY';
    public const string EXCHANGE_RATES_URL     = 'EXCHANGE_RATES_URL';
    public const string EXCHANGE_RATES_API_KEY = 'EXCHANGE_RATES_API_KEY';
    public const string EU_COUNTRIES           = 'EU_COUNTRIES';
    public const string EU_COMMISSION_RATE     = 'EU_COMMISSION_RATE';
    public const string NON_EU_COMMISSION_RATE = 'NON_EU_COMMISSION_RATE';
}
