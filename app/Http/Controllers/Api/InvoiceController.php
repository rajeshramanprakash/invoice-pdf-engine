<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateInvoiceRequest;
use App\Services\InvoicePdfService;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoicePdfService $invoicePdfService,
    ) {}

    public function generate(GenerateInvoiceRequest $request): Response
    {
        $pdf = $this->invoicePdfService->generate($request->validated());

        // Content-Disposition rejects "/" and "\", and invoice numbers like "INV/2026/001" are common.
        $invoiceNumber = trim(preg_replace('/[^A-Za-z0-9._-]+/', '-', $request->validated('invoice_number')), '-');

        return $pdf->download("invoice-{$invoiceNumber}.pdf");
    }
}
