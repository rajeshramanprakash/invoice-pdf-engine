<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;

class InvoicePdfService
{
    /**
     * Build the invoice PDF from validated request data.
     *
     * @param  array<string, mixed>  $data
     */
    public function generate(array $data): DomPdf
    {
        return Pdf::loadView('pdf.invoice', $this->buildViewData($data))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');
    }

    /**
     * Compute line totals, subtotal, tax and grand total.
     *
     * Arithmetic is done in integer minor units (cents) so that the printed
     * line totals always add up exactly to the printed subtotal.
     *
     * @param  array<int, array{description: string, quantity: int|string, unit_price: int|float|string}>  $items
     * @return array{items: array<int, array{description: string, quantity: int, unit_price: float, line_total: float}>, subtotal: float, tax_percent: float, tax_amount: float, grand_total: float}
     */
    public function calculateTotals(array $items, int|float|string|null $taxPercent = null): array
    {
        $lines = [];
        $subtotalCents = 0;

        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            $unitPriceCents = $this->toCents($item['unit_price']);
            $lineTotalCents = $quantity * $unitPriceCents;

            $subtotalCents += $lineTotalCents;

            $lines[] = [
                'description' => (string) $item['description'],
                'quantity' => $quantity,
                'unit_price' => $this->fromCents($unitPriceCents),
                'line_total' => $this->fromCents($lineTotalCents),
            ];
        }

        $taxPercent = (float) ($taxPercent ?? 0);
        $taxCents = (int) round($subtotalCents * $taxPercent / 100, 0, PHP_ROUND_HALF_UP);

        return [
            'items' => $lines,
            'subtotal' => $this->fromCents($subtotalCents),
            'tax_percent' => $taxPercent,
            'tax_amount' => $this->fromCents($taxCents),
            'grand_total' => $this->fromCents($subtotalCents + $taxCents),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildViewData(array $data): array
    {
        $issuedAt = Carbon::now();

        return [
            'invoice' => [
                'number' => $data['invoice_number'],
                'issued_at' => $issuedAt,
                'due_at' => $issuedAt->copy()->addDays(config('invoice.payment_terms_days')),
            ],
            'client' => [
                'name' => $data['client_name'],
                'email' => $data['client_email'],
                'address' => $data['client_address'],
            ],
            'totals' => $this->calculateTotals($data['items'], $data['tax_percent'] ?? null),
            'company' => config('invoice.company'),
            'currency' => config('invoice.currency'),
            'footerNote' => config('invoice.footer_note'),
        ];
    }

    private function toCents(int|float|string $amount): int
    {
        return (int) round((float) $amount * 100, 0, PHP_ROUND_HALF_UP);
    }

    private function fromCents(int $cents): float
    {
        return $cents / 100;
    }
}
