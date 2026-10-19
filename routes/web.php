<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Invoice PDF Engine',
        'description' => 'Turns a JSON billing payload into a print-ready A4 invoice PDF.',
        'endpoint' => [
            'method' => 'POST',
            'url' => url('/api/v1/invoices/generate-pdf'),
        ],
        'documentation' => 'https://github.com/rajeshramanprakash/invoice-pdf-engine#api-reference',
        'health' => url('/up'),
    ]);
});
