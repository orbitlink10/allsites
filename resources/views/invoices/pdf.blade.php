<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #17202a;
            margin: 0;
        }
        .header {
            border-bottom: 2px solid #027333;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }
        .brand {
            float: left;
            width: 55%;
        }
        .brand-logo {
            float: left;
            width: 70px;
            height: 70px;
            margin-right: 12px;
            border: 1px solid #e4e7ec;
            padding: 5px;
            object-fit: contain;
        }
        .brand-copy {
            overflow: hidden;
        }
        .meta {
            float: right;
            width: 40%;
            text-align: right;
        }
        .clear { clear: both; }
        h1, h2, h3, p { margin-top: 0; }
        h1 { color: #027333; font-size: 24px; margin-bottom: 6px; }
        h2 { font-size: 22px; margin-bottom: 6px; }
        h3 { font-size: 13px; color: #667085; text-transform: uppercase; margin-bottom: 8px; }
        .muted { color: #667085; }
        .amount-due {
            margin-top: 12px;
            color: #027333;
            font-size: 18px;
            font-weight: bold;
        }
        .amount-label {
            color: #667085;
            font-size: 10px;
            text-transform: uppercase;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .status-success { background: #027a48; }
        .status-warning { background: #b54708; }
        .status-danger { background: #b42318; }
        .status-secondary, .status-dark { background: #667085; }
        .columns { margin-bottom: 24px; }
        .column {
            float: left;
            width: 48%;
        }
        .column + .column {
            margin-left: 4%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border-bottom: 1px solid #e4e7ec;
            padding: 10px 8px;
            text-align: left;
        }
        th {
            background: #f5f7f8;
            color: #667085;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        tfoot th, tfoot td {
            font-size: 14px;
            border-bottom: 0;
            font-weight: bold;
        }
        .notes {
            margin-top: 24px;
            color: #667085;
        }
        .payment-box {
            margin-top: 18px;
            padding: 12px;
            border: 1px solid #abefc6;
            background: #f6fef9;
        }
        .payment-box h3 {
            color: #027333;
            margin-bottom: 8px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .payment-table td {
            border: 0;
            padding: 4px 8px 4px 0;
        }
        .payment-label {
            color: #667085;
            font-size: 10px;
            text-transform: uppercase;
        }
        .payment-value {
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #e4e7ec;
            padding-top: 10px;
            color: #667085;
            font-size: 10px;
            text-align: center;
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

    <div class="header">
        <div class="brand">
            @if($siteLogo !== '')
                <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="brand-logo">
            @endif
            <div class="brand-copy">
                <h1>{{ $siteName }}</h1>
                @if($companyPhone !== '' || $companyEmail !== '')
                    <p class="muted">
                        {{ $companyPhone }}
                        @if($companyPhone !== '' && $companyEmail !== '') &middot; @endif
                        {{ $companyEmail }}
                    </p>
                @endif
                @if($companyAddress !== '')
                    <p class="muted">{{ $companyAddress }}</p>
                @endif
            </div>
            @if($siteLogo !== '')
                <div class="clear"></div>
            @endif
        </div>
        <div class="meta">
            <h2>Invoice</h2>
            <p><strong>{{ $invoice->invoice_number }}</strong></p>
            <span class="status status-{{ $invoice->statusClass() }}">{{ $invoice->statusLabel() }}</span>
            <div class="amount-due">
                <div class="amount-label">Amount Due</div>
                {{ $invoice->formattedTotal() }}
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="columns">
        <div class="column">
            <h3>Billed To</h3>
            <p><strong>{{ $invoice->client_name ?: 'Customer' }}</strong></p>
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
        <div class="column">
            <h3>Invoice Details</h3>
            <p><strong>Issued:</strong> {{ optional($invoice->created_at)->format('M d, Y') }}</p>
            <p><strong>Due:</strong> {{ optional($invoice->due_date)->format('M d, Y') ?: 'On receipt' }}</p>
            @if($invoice->paid_at)
                <p><strong>Paid:</strong> {{ $invoice->paid_at->format('M d, Y') }}</p>
            @endif
            <p><strong>Reference:</strong> {{ $invoice->name }}</p>
        </div>
        <div class="clear"></div>
    </div>

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
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ number_format((float) $item->quantity, 2) }}</td>
                    <td class="text-right">{{ $invoice->formatMoney($item->unit_price) }}</td>
                    <td class="text-right">{{ $invoice->formatMoney($item->lineTotal()) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No line items found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <td class="text-right">{{ $invoice->formattedTotal() }}</td>
            </tr>
        </tfoot>
    </table>

    @if($mpesaPaymentNumber !== '')
        <div class="payment-box">
            <h3>MPESA Payment</h3>
            <p class="muted">Send payment to the number below and use the invoice number as the reference.</p>
            <table class="payment-table">
                <tr>
                    <td class="payment-label">Pay To</td>
                    <td class="payment-value">{{ $mpesaPaymentNumber }}</td>
                    <td class="payment-label">Reference</td>
                    <td class="payment-value">{{ $mpesaReference }}</td>
                    <td class="payment-label">Amount</td>
                    <td class="payment-value">{{ $invoice->formattedTotal() }}</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="notes">
        <p>{{ $invoice->notes ?: 'Thank you for choosing Gamun. Please contact us if you have questions about this invoice.' }}</p>
    </div>

    <div class="footer">
        {{ $siteName }} &middot; {{ url('/') }}
    </div>
</body>
</html>
