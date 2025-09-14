<?php

if (! function_exists('format_number_for_display')) {
    function format_number_for_display($amount)
    {
        // Use es-CO locale for dot as thousands separator and comma as decimal separator
        $formatter = new NumberFormatter('es-CO', NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0); // Minimum 0 decimal places
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2); // Maximum 2 decimal places

        // Handle cases like 50000.00 -> 50.000
        // And 50000.50 -> 50.000,50
        // And 50000 -> 50.000
        if (is_numeric($amount)) {
            return $formatter->format($amount);
        }
        return $amount; // Return as is if not a number
    }
}
