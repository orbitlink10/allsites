<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Invoice {{ $invoice->invoice_number }} | {{ get_option('site_name', 'Gamun') }}</title>
    <style>
        :root {
            --brand: #027333;
            --brand-ink: #07542a;
            --ink: #101828;
            --muted: #667085;
            --soft: #f6f8f9;
            --line: #d9e0e5;
            --paper: #ffffff;
            --warning-bg: #fff7df;
            --warning: #a15c00;
            --success-bg: #eaf8ef;
            --success: #087443;
            --danger-bg: #fff0ed;
            --danger: #b42318;
            --neutral-bg: #eef2f5;
            --neutral: #344054;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            background: #eef2f5;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            line-height: 1.5;
        }
        .page {
            max-width: 1040px;
            margin: 40px auto;
            padding: 0 18px;
        }
        .notice {
            margin-bottom: 16px;
            padding: 12px 16px;
            border: 1px solid #abefc6;
            border-radius: 6px;
            background: #ecfdf3;
            color: #027a48;
        }
        .invoice {
            background: var(--paper);
            border: 0;
            border-radius: 8px;
            box-shadow: 0 16px 36px rgba(16, 24, 40, 0.08);
            overflow: hidden;
        }
        .invoice-head {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
            gap: 32px;
            padding: 34px 38px 30px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(135deg, #ffffff 0%, #f3faf6 100%);
        }
        .invoice-head::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--brand);
        }
        .company {
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }
        .logo-shell {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 84px;
            height: 84px;
            flex: 0 0 84px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 8px 18px rgba(16, 24, 40, 0.06);
        }
        .logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }
        .logo-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 8px;
            background: var(--brand);
            color: #fff;
            font-size: 30px;
            font-weight: 800;
        }
        h1, h2, h3, p {
            margin-top: 0;
        }
        .company-name {
            margin: 0 0 6px;
            color: var(--brand-ink);
            font-size: 28px;
            letter-spacing: 0;
        }
        .brand-eyebrow {
            margin: 0 0 5px;
            color: var(--brand);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }
        .muted {
            color: var(--muted);
        }
        .compact p {
            margin-bottom: 3px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            margin: 0;
            font-size: 34px;
            letter-spacing: 0;
        }
        .document-label {
            margin: 0 0 4px;
            color: var(--brand);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .invoice-number {
            color: var(--muted);
            font-weight: 700;
        }
        .amount-due {
            margin-top: 18px;
            color: var(--brand-ink);
            font-size: 26px;
            font-weight: 800;
        }
        .amount-label {
            margin-bottom: 2px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .status {
            display: inline-block;
            margin-top: 14px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }
        .status-success {
            color: var(--success);
            background: var(--success-bg);
        }
        .status-warning {
            color: var(--warning);
            background: var(--warning-bg);
        }
        .status-danger {
            color: var(--danger);
            background: var(--danger-bg);
        }
        .status-secondary,
        .status-dark {
            color: var(--neutral);
            background: var(--neutral-bg);
        }
        .invoice-body {
            padding: 32px 38px 34px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 22px;
            margin-bottom: 30px;
        }
        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
            background: #fff;
        }
        .panel-title {
            margin: 0 0 12px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding: 8px 0;
            border-bottom: 1px solid #edf1f4;
        }
        .detail-row:last-child {
            border-bottom: 0;
        }
        .detail-label {
            color: var(--muted);
        }
        .detail-value {
            text-align: right;
            font-weight: 700;
        }
        .items-wrap {
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
        }
        th {
            background: var(--soft);
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
        }
        tbody tr:last-child td {
            border-bottom: 0;
        }
        .text-right {
            text-align: right;
        }
        .item-description {
            font-weight: 700;
        }
        .item-meta {
            color: var(--muted);
            font-size: 13px;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            padding: 18px 16px;
            background: #fbfcfd;
            border-top: 1px solid var(--line);
        }
        .total-box {
            min-width: 280px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            font-size: 20px;
            font-weight: 800;
        }
        .notes {
            margin-top: 28px;
            padding: 18px;
            border-left: 4px solid var(--brand);
            background: #f7fbf8;
            border-radius: 6px;
            color: var(--muted);
        }
        .payment-panel {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            margin-top: 22px;
            padding: 20px;
            border: 1px solid #abefc6;
            border-radius: 8px;
            background: #f6fef9;
        }
        .payment-panel h3 {
            margin: 0 0 6px;
            color: var(--brand-ink);
            font-size: 18px;
        }
        .payment-panel p {
            margin-bottom: 0;
        }
        .payment-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(130px, 1fr));
            gap: 12px;
            min-width: 420px;
        }
        .payment-field {
            padding: 12px;
            border: 1px solid #d1fadf;
            border-radius: 6px;
            background: #fff;
        }
        .payment-label {
            display: block;
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }
        .payment-value {
            display: block;
            color: var(--ink);
            font-size: 14px;
            font-weight: 800;
            word-break: break-word;
        }
        .payment-number {
            color: var(--brand);
            font-size: 18px;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px 38px;
            border-top: 1px solid var(--line);
            background: #fbfcfd;
        }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 16px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: #fff;
            color: var(--ink);
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }
        .button-primary {
            border-color: var(--brand);
            background: var(--brand);
            color: #fff;
        }
        @media (max-width: 760px) {
            .page {
                margin: 18px auto;
                padding: 0 10px;
            }
            .invoice-head,
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .payment-panel,
            .payment-grid {
                grid-template-columns: 1fr;
                min-width: 0;
            }
            .invoice-head,
            .invoice-body,
            .actions {
                padding-left: 20px;
                padding-right: 20px;
            }
            .invoice-title,
            .detail-value {
                text-align: left;
            }
            .company {
                flex-direction: column;
            }
            .detail-row {
                flex-direction: column;
                gap: 2px;
            }
            th:nth-child(3),
            td:nth-child(3) {
                display: none;
            }
            .actions {
                justify-content: stretch;
                flex-direction: column;
            }
            .button {
                width: 100%;
            }
        }
        @media print {
            body {
                background: #fff;
            }
            .page {
                max-width: none;
                margin: 0;
                padding: 0;
            }
            .invoice {
                box-shadow: none;
                border: 0;
            }
            .actions,
            .notice {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        $siteName = trim((string) get_option('site_name', 'Gamun')) ?: 'Gamun';
        $siteLogo = trim((string) get_option('logo', ''));
        $siteLogo = $siteLogo === 'logo' ? '' : $siteLogo;
        $companyAddress = trim((string) get_option('address', ''));
        $companyPhone = trim((string) get_option('contact_phone', ''));
        $companyEmail = trim((string) get_option('contact_email', ''));
        $mpesaPaymentNumber = trim((string) (get_option('mpesa_payment_number', '') ?: $companyPhone));
        $mpesaReference = $invoice->invoice_number ?: ('Invoice #' . $invoice->id);
    @endphp

    <main class="page">
        @if(session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif

        <article class="invoice">
            <header class="invoice-head">
                <div class="company">
                    <div class="logo-shell">
                        @if($siteLogo !== '')
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="logo">
                        @else
                            <span class="logo-fallback">{{ strtoupper(substr($siteName, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="compact">
                        <p class="brand-eyebrow">Official Invoice</p>
                        <h1 class="company-name">{{ $siteName }}</h1>
                        @if($companyAddress !== '')
                            <p class="muted">{{ $companyAddress }}</p>
                        @endif
                        @if($companyPhone !== '')
                            <p class="muted">{{ $companyPhone }}</p>
                        @endif
                        @if($companyEmail !== '')
                            <p class="muted">{{ $companyEmail }}</p>
                        @endif
                    </div>
                </div>

                <div class="invoice-title">
                    <p class="document-label">Invoice</p>
                    <h2>Invoice</h2>
                    <p class="invoice-number">{{ $invoice->invoice_number ?: ('#' . $invoice->id) }}</p>
                    <span class="status status-{{ $invoice->statusClass() }}">{{ $invoice->statusLabel() }}</span>
                    <div class="amount-due">
                        <div class="amount-label">Amount Due</div>
                        {{ $invoice->formattedTotal() }}
                    </div>
                </div>
            </header>

            <section class="invoice-body">
                <div class="summary-grid">
                    <div class="panel compact">
                        <h3 class="panel-title">Billed To</h3>
                        <p><strong>{{ $invoice->client_name ?: $invoice->name ?: 'Customer' }}</strong></p>
                        @if($invoice->client_address)
                            <p>{!! nl2br(e($invoice->client_address)) !!}</p>
                        @endif
                        @if($invoice->client_email)
                            <p>{{ $invoice->client_email }}</p>
                        @endif
                        @if($invoice->client_phone)
                            <p>{{ $invoice->client_phone }}</p>
                        @endif
                    </div>

                    <div class="panel">
                        <h3 class="panel-title">Invoice Details</h3>
                        <div class="detail-row">
                            <span class="detail-label">Issued</span>
                            <span class="detail-value">{{ optional($invoice->created_at)->format('M d, Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Due</span>
                            <span class="detail-value">{{ optional($invoice->due_date)->format('M d, Y') ?: 'On receipt' }}</span>
                        </div>
                        @if($invoice->paid_at)
                            <div class="detail-row">
                                <span class="detail-label">Paid</span>
                                <span class="detail-value">{{ $invoice->paid_at->format('M d, Y') }}</span>
                            </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Reference</span>
                            <span class="detail-value">{{ $invoice->name }}</span>
                        </div>
                    </div>
                </div>

                <div class="items-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Rate</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->items as $item)
                                <tr>
                                    <td>
                                        <div class="item-description">{{ $item->description }}</div>
                                    </td>
                                    <td class="text-right">{{ number_format((float) $item->quantity, 2) }}</td>
                                    <td class="text-right">{{ $invoice->formatMoney($item->unit_price) }}</td>
                                    <td class="text-right"><strong>{{ $invoice->formatMoney($item->lineTotal()) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted">No line items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="totals">
                        <div class="total-box">
                            <div class="total-row">
                                <span>Total</span>
                                <span>{{ $invoice->formattedTotal() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($mpesaPaymentNumber !== '')
                    <div class="payment-panel">
                        <div>
                            <h3>MPESA Payment</h3>
                            <p class="muted">Send payment to the number below and use the invoice number as the reference.</p>
                        </div>
                        <div class="payment-grid">
                            <div class="payment-field">
                                <span class="payment-label">Pay To</span>
                                <span class="payment-value payment-number">{{ $mpesaPaymentNumber }}</span>
                            </div>
                            <div class="payment-field">
                                <span class="payment-label">Reference</span>
                                <span class="payment-value">{{ $mpesaReference }}</span>
                            </div>
                            <div class="payment-field">
                                <span class="payment-label">Amount</span>
                                <span class="payment-value">{{ $invoice->formattedTotal() }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="notes">
                    {{ $invoice->notes ?: 'Thank you for choosing Gamun. Please contact us if you have questions about this invoice.' }}
                </div>
            </section>

            <footer class="actions">
                <button type="button" class="button" onclick="window.print()">Print</button>
                <a href="{{ route('invoices.public.download', $invoice->slug) }}" class="button button-primary">Download PDF</a>
            </footer>
        </article>
    </main>
</body>
</html>
