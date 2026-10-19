<?php

namespace Tests\Unit;

use App\Services\InvoicePdfService;
use PHPUnit\Framework\TestCase;

class InvoicePdfServiceTest extends TestCase
{
    private InvoicePdfService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoicePdfService;
    }

    public function test_it_calculates_line_totals_subtotal_tax_and_grand_total(): void
    {
        $totals = $this->service->calculateTotals([
            ['description' => 'Design', 'quantity' => 2, 'unit_price' => 150],
            ['description' => 'Hosting', 'quantity' => 3, 'unit_price' => '19.99'],
        ], 18);

        $this->assertSame(300.0, $totals['items'][0]['line_total']);
        $this->assertSame(59.97, $totals['items'][1]['line_total']);
        $this->assertSame(359.97, $totals['subtotal']);
        $this->assertSame(64.79, $totals['tax_amount']);
        $this->assertSame(424.76, $totals['grand_total']);
    }

    public function test_missing_tax_percent_means_zero_tax(): void
    {
        $totals = $this->service->calculateTotals([
            ['description' => 'Consulting', 'quantity' => 1, 'unit_price' => 99.5],
        ]);

        $this->assertSame(0.0, $totals['tax_percent']);
        $this->assertSame(0.0, $totals['tax_amount']);
        $this->assertSame(99.5, $totals['grand_total']);
    }

    public function test_it_avoids_floating_point_drift(): void
    {
        $totals = $this->service->calculateTotals([
            ['description' => 'A', 'quantity' => 1, 'unit_price' => 0.1],
            ['description' => 'B', 'quantity' => 1, 'unit_price' => 0.2],
        ], 0);

        $this->assertSame(0.3, $totals['subtotal']);
    }

    public function test_tax_rounds_half_up_to_the_cent(): void
    {
        $totals = $this->service->calculateTotals([
            ['description' => 'Widget', 'quantity' => 1, 'unit_price' => 0.5],
        ], 5);

        $this->assertSame(0.03, $totals['tax_amount']);
    }
}
