<?php

namespace Tests\Feature;

use Tests\TestCase;

class GenerateInvoicePdfTest extends TestCase
{
    private const ENDPOINT = '/api/v1/invoices/generate-pdf';

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'invoice_number' => 'INV-2026-0001',
            'client_name' => 'Globex Corporation',
            'client_email' => 'accounts@globex.example',
            'client_address' => "221B Baker Street\nLondon NW1 6XE",
            'items' => [
                ['description' => 'Website redesign', 'quantity' => 1, 'unit_price' => 2500],
                ['description' => 'Monthly hosting', 'quantity' => 12, 'unit_price' => 29.99],
            ],
            'tax_percent' => 18,
        ], $overrides);
    }

    public function test_it_downloads_a_pdf_invoice(): void
    {
        $response = $this->postJson(self::ENDPOINT, $this->validPayload());

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString(
            'attachment; filename=invoice-INV-2026-0001.pdf',
            $response->headers->get('Content-Disposition'),
        );
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_it_renders_unicode_content(): void
    {
        $response = $this->postJson(self::ENDPOINT, $this->validPayload([
            'client_name' => 'Zürich Café GmbH — ₹ € £ Ωμέγα',
        ]));

        $response->assertOk();
    }

    public function test_invoice_numbers_with_slashes_produce_a_safe_filename(): void
    {
        $response = $this->postJson(self::ENDPOINT, $this->validPayload([
            'invoice_number' => 'INV/2026/0001',
        ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'filename=invoice-INV-2026-0001.pdf',
            $response->headers->get('Content-Disposition'),
        );
    }

    public function test_tax_percent_is_optional(): void
    {
        $payload = $this->validPayload();
        unset($payload['tax_percent']);

        $this->postJson(self::ENDPOINT, $payload)->assertOk();
    }

    public function test_it_requires_the_core_fields(): void
    {
        $this->postJson(self::ENDPOINT, [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'invoice_number', 'client_name', 'client_email', 'client_address', 'items',
            ]);
    }

    public function test_it_validates_line_items_and_tax(): void
    {
        $this->postJson(self::ENDPOINT, $this->validPayload([
            'client_email' => 'not-an-email',
            'items' => [
                ['description' => '', 'quantity' => 0, 'unit_price' => -5],
            ],
            'tax_percent' => 150,
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'client_email',
                'items.0.description',
                'items.0.quantity',
                'items.0.unit_price',
                'tax_percent',
            ]);
    }

    public function test_items_must_not_be_empty(): void
    {
        $this->postJson(self::ENDPOINT, $this->validPayload(['items' => []]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
    }

    public function test_it_limits_the_size_of_a_request(): void
    {
        $item = ['description' => str_repeat('a', 501), 'quantity' => 1, 'unit_price' => 1];

        $this->postJson(self::ENDPOINT, $this->validPayload(['items' => array_fill(0, 101, $item)]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items', 'items.0.description']);
    }

    public function test_it_rate_limits_requests_per_ip(): void
    {
        config(['invoice.rate_limit_per_minute' => 2]);

        $this->postJson(self::ENDPOINT, $this->validPayload())->assertOk();
        $this->postJson(self::ENDPOINT, $this->validPayload())->assertOk();
        $this->postJson(self::ENDPOINT, $this->validPayload())->assertTooManyRequests();
    }
}
