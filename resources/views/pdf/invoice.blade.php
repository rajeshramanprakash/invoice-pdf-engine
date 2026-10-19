<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Invoice {{ $invoice['number'] }}</title>
    <style>
        @page {
            margin: 36px 40px 70px 40px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.45;
            color: #1f2937;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            vertical-align: top;
        }

        .accent-bar {
            height: 6px;
            background-color: #1e3a5f;
            margin-bottom: 22px;
        }

        /* Header */
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            margin: 0 0 4px 0;
        }

        .company-meta {
            color: #6b7280;
            font-size: 9px;
        }

        .doc-title {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #1e3a5f;
            text-align: right;
            margin: 0;
        }

        .doc-number {
            text-align: right;
            color: #6b7280;
            font-size: 10px;
            margin-top: 2px;
        }

        /* Parties and metadata */
        .section {
            margin-top: 26px;
        }

        .label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .client-name {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 2px;
        }

        .meta-table td {
            padding: 3px 0;
        }

        .meta-table .meta-key {
            color: #6b7280;
            text-align: left;
        }

        .meta-table .meta-value {
            text-align: right;
            font-weight: bold;
            color: #111827;
        }

        .meta-box {
            background-color: #f3f6fa;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
        }

        /* Line items */
        .items {
            margin-top: 28px;
        }

        .items thead th {
            background-color: #1e3a5f;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #1e3a5f;
        }

        .items tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .items tbody tr {
            page-break-inside: avoid;
        }

        .items tbody tr.striped td {
            background-color: #f9fafb;
        }

        .items .col-index {
            width: 6%;
            color: #9ca3af;
        }

        .items .col-description {
            width: 48%;
            word-wrap: break-word;
        }

        .items .col-qty {
            width: 10%;
            text-align: center;
        }

        .items .col-price,
        .items .col-total {
            width: 18%;
            text-align: right;
        }

        .items .col-total {
            font-weight: bold;
        }

        /* Summary */
        .summary-wrapper {
            margin-top: 22px;
            page-break-inside: avoid;
        }

        .notes {
            padding-right: 30px;
            color: #4b5563;
            font-size: 9px;
        }

        .totals {
            border: 1px solid #e2e8f0;
        }

        .totals td {
            padding: 7px 12px;
        }

        .totals .totals-key {
            color: #4b5563;
        }

        .totals .totals-value {
            text-align: right;
            color: #111827;
        }

        .totals tr.divider td {
            border-top: 1px solid #e2e8f0;
        }

        .totals tr.grand-total td {
            background-color: #1e3a5f;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 10px 12px;
            border: 1px solid #1e3a5f;
        }

        /* Footer */
        footer {
            position: fixed;
            bottom: -46px;
            left: 0;
            right: 0;
            height: 36px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 8px;
            color: #9ca3af;
        }

        footer .footer-right {
            text-align: right;
        }
    </style>
</head>
<body>
@php
    $money = fn (float $amount): string => $currency['symbol'] . number_format($amount, 2);
    $formatPercent = fn (float $percent): string => rtrim(rtrim(number_format($percent, 2), '0'), '.');
@endphp

<footer>
    <table>
        <tr>
            <td>{{ $company['name'] }} &middot; {{ $company['email'] }} &middot; {{ $company['website'] }}</td>
            <td class="footer-right">Invoice {{ $invoice['number'] }}</td>
        </tr>
    </table>
</footer>

<div class="accent-bar"></div>

{{-- Company header --}}
<table>
    <tr>
        <td style="width: 60%;">
            <p class="company-name">{{ $company['name'] }}</p>
            <div class="company-meta">
                {!! nl2br(e($company['address'])) !!}<br>
                {{ $company['phone'] }} &middot; {{ $company['email'] }}
                @if (! empty($company['tax_id']))
                    <br>Tax ID: {{ $company['tax_id'] }}
                @endif
            </div>
        </td>
        <td style="width: 40%;">
            <p class="doc-title">INVOICE</p>
            <div class="doc-number"># {{ $invoice['number'] }}</div>
        </td>
    </tr>
</table>

{{-- Client details and invoice metadata --}}
<table class="section">
    <tr>
        <td style="width: 55%; padding-right: 30px;">
            <div class="label">Billed To</div>
            <div class="client-name">{{ $client['name'] }}</div>
            <div>{!! nl2br(e($client['address'])) !!}</div>
            <div style="color: #1e3a5f; margin-top: 2px;">{{ $client['email'] }}</div>
        </td>
        <td style="width: 45%;">
            <div class="meta-box">
                <table class="meta-table">
                    <tr>
                        <td class="meta-key">Invoice No.</td>
                        <td class="meta-value">{{ $invoice['number'] }}</td>
                    </tr>
                    <tr>
                        <td class="meta-key">Issue Date</td>
                        <td class="meta-value">{{ $invoice['issued_at']->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-key">Due Date</td>
                        <td class="meta-value">{{ $invoice['due_at']->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-key">Amount Due</td>
                        <td class="meta-value">{{ $money($totals['grand_total']) }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

{{-- Line items --}}
<table class="items">
    <thead>
        <tr>
            <th class="col-index">#</th>
            <th class="col-description">Description</th>
            <th class="col-qty">Qty</th>
            <th class="col-price">Unit Price</th>
            <th class="col-total">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($totals['items'] as $item)
            <tr @class(['striped' => $loop->even])>
                <td class="col-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                <td class="col-description">{{ $item['description'] }}</td>
                <td class="col-qty">{{ $item['quantity'] }}</td>
                <td class="col-price">{{ $money($item['unit_price']) }}</td>
                <td class="col-total">{{ $money($item['line_total']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Notes and total summary box --}}
<table class="summary-wrapper">
    <tr>
        <td class="notes" style="width: 55%;">
            <div class="label">Notes</div>
            <div>{{ $footerNote }}</div>
            <div style="margin-top: 8px;">
                Payment is due by <strong>{{ $invoice['due_at']->format('d M Y') }}</strong>.
                All amounts are in {{ $currency['code'] }}.
            </div>
        </td>
        <td style="width: 45%;">
            <table class="totals">
                <tr>
                    <td class="totals-key">Subtotal</td>
                    <td class="totals-value">{{ $money($totals['subtotal']) }}</td>
                </tr>
                <tr class="divider">
                    <td class="totals-key">Tax ({{ $formatPercent($totals['tax_percent']) }}%)</td>
                    <td class="totals-value">{{ $money($totals['tax_amount']) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Due</td>
                    <td style="text-align: right;">{{ $money($totals['grand_total']) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
