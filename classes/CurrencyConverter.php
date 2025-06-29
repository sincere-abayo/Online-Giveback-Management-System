<?php
/**
 * Simple Currency Converter
 * Academic Purpose - Basic Implementation
 * Supports USD and RWF conversion
 */

class CurrencyConverter
{
    // Fixed exchange rate for academic purposes (approximate)
    private const USD_TO_RWF_RATE = 1300; // 1 USD = 1300 RWF (approximate)
    
    private $supportedCurrencies = ['USD', 'RWF'];
    
    /**
     * Convert amount from one currency to another
     */
    public function convert($amount, $fromCurrency, $toCurrency)
    {
        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);
        
        // Validate currencies
        if (!in_array($fromCurrency, $this->supportedCurrencies) || 
            !in_array($toCurrency, $this->supportedCurrencies)) {
            throw new Exception("Unsupported currency. Only USD and RWF are supported.");
        }
        
        // If same currency, return original amount
        if ($fromCurrency === $toCurrency) {
            return round($amount, 2);
        }
        
        // Convert based on fixed rates
        if ($fromCurrency === 'USD' && $toCurrency === 'RWF') {
            return round($amount * self::USD_TO_RWF_RATE, 0); // RWF doesn't use decimals
        } elseif ($fromCurrency === 'RWF' && $toCurrency === 'USD') {
            return round($amount / self::USD_TO_RWF_RATE, 2);
        }
        
        return $amount;
    }
    
    /**
     * Format amount with currency symbol
     */
    public function formatAmount($amount, $currency)
    {
        $currency = strtoupper($currency);
        
        switch ($currency) {
            case 'USD':
                return '$' . number_format($amount, 2);
            case 'RWF':
                return number_format($amount, 0) . ' RWF';
            default:
                return number_format($amount, 2) . ' ' . $currency;
        }
    }
    
    /**
     * Get currency symbol
     */
    public function getCurrencySymbol($currency)
    {
        $currency = strtoupper($currency);
        
        switch ($currency) {
            case 'USD':
                return '$';
            case 'RWF':
                return 'RWF';
            default:
                return $currency;
        }
    }
    
    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies()
    {
        return $this->supportedCurrencies;
    }
    
    /**
     * Get current exchange rate
     */
    public function getExchangeRate($fromCurrency, $toCurrency)
    {
        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);
        
        if ($fromCurrency === $toCurrency) {
            return 1;
        }
        
        if ($fromCurrency === 'USD' && $toCurrency === 'RWF') {
            return self::USD_TO_RWF_RATE;
        } elseif ($fromCurrency === 'RWF' && $toCurrency === 'USD') {
            return 1 / self::USD_TO_RWF_RATE;
        }
        
        return 1;
    }
    
    /**
     * Get minimum donation amount for currency
     */
    public function getMinimumAmount($currency)
    {
        $currency = strtoupper($currency);
        
        switch ($currency) {
            case 'USD':
                return 1; // $1 minimum
            case 'RWF':
                return 100; // 100 RWF minimum
            default:
                return 1;
        }
    }
    
    /**
     * Validate if amount meets minimum requirement
     */
    public function isValidAmount($amount, $currency)
    {
        $minAmount = $this->getMinimumAmount($currency);
        return $amount >= $minAmount;
    }
}
?>
