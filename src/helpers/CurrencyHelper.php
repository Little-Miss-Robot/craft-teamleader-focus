<?php
/**
 * Teamleader plugin for Craft CMS
 *
 * @link      https://craft-pulse.com
 * @copyright Copyright (c) 2025 CraftPulse
 */

namespace craftpulse\teamleader\helpers;

/**
 * Class CurrencyHelper
 *
 * @author      CraftPulse
 * @package     Teamleader
 * @since       5.2.0
 */
class CurrencyHelper
{
    /**
     * Default fallback currencies if API call fails.
     */
    private const DEFAULT_CURRENCIES = [
        ['label' => 'EUR - Euro', 'value' => 'EUR'],
    ];

    /**
     * Format currency response data into options array.
     *
     * @param array $data Raw API response data from currencies.exchangeRates
     * @return array Formatted options for select fields
     */
    public static function formatCurrencyOptions(array $data): array
    {
        $currencies = [];

        foreach ($data as $currency) {
            $currencies[] = [
                'label' => $currency['code'] . ' - ' . $currency['name'],
                'value' => $currency['code'],
            ];
        }

        return !empty($currencies) ? $currencies : self::DEFAULT_CURRENCIES;
    }

    /**
     * Get the default fallback currencies.
     *
     * @return array
     */
    public static function getDefaultCurrencies(): array
    {
        return self::DEFAULT_CURRENCIES;
    }
}
