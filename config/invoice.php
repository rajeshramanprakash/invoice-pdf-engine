<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Issuing Company
    |--------------------------------------------------------------------------
    |
    | Printed in the header and footer of every generated invoice PDF.
    |
    */

    'company' => [
        'name' => env('INVOICE_COMPANY_NAME', 'Acme Solutions Pvt. Ltd.'),
        'address' => env('INVOICE_COMPANY_ADDRESS', "42 Innovation Park, Tech Road\nBengaluru, KA 560001, India"),
        'email' => env('INVOICE_COMPANY_EMAIL', 'billing@acme.example'),
        'phone' => env('INVOICE_COMPANY_PHONE', '+91 80 1234 5678'),
        'website' => env('INVOICE_COMPANY_WEBSITE', 'www.acme.example'),
        'tax_id' => env('INVOICE_COMPANY_TAX_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Defaults
    |--------------------------------------------------------------------------
    */

    'currency' => [
        'code' => env('INVOICE_CURRENCY_CODE', 'INR'),
        'symbol' => env('INVOICE_CURRENCY_SYMBOL', '₹'),
    ],

    'payment_terms_days' => (int) env('INVOICE_PAYMENT_TERMS_DAYS', 14),

    'footer_note' => env('INVOICE_FOOTER_NOTE', 'Thank you for your business. Please include the invoice number with your payment.'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum PDF generation requests per minute from a single IP address.
    |
    */

    'rate_limit_per_minute' => (int) env('INVOICE_RATE_LIMIT_PER_MINUTE', 10),

];
