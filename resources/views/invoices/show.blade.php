@extends('layouts.appbar')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="page-title">{{ $invoice->invoice_number }}</h1>
                    <p class="text-muted mb-0">{{ $invoice->name }}</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('invoices.open', $invoice->slug) }}" target="_blank" class="btn btn-outline-success">
                        <i class="fas fa-external-link-alt"></i> Public View
                    </a>
                    <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-file-download"></i> PDF
                    </a>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold mb-0">Line Items</h3>
                                <span class="badge badge-{{ $invoice->statusClass() }}">{{ $invoice->statusLabel() }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="thead-light">
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
                                                    <td class="text-right font-weight-bold">{{ $invoice->formatMoney($item->lineTotal()) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">No line items have been added.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-right">Total</th>
                                                <th class="text-right">{{ $invoice->formattedTotal() }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h3 class="card-title font-weight-bold mb-0">Invoice Summary</h3>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-5">Invoice no</dt>
                                    <dd class="col-7">{{ $invoice->invoice_number }}</dd>
                                    <dt class="col-5">Issued</dt>
                                    <dd class="col-7">{{ optional($invoice->created_at)->format('M d, Y') }}</dd>
                                    <dt class="col-5">Due</dt>
                                    <dd class="col-7">{{ optional($invoice->due_date)->format('M d, Y') ?: 'Not set' }}</dd>
                                    <dt class="col-5">Client</dt>
                                    <dd class="col-7">{{ $invoice->client_name ?: 'Not set' }}</dd>
                                    <dt class="col-5">Email</dt>
                                    <dd class="col-7">{{ $invoice->client_email ?: 'Not set' }}</dd>
                                    <dt class="col-5">Phone</dt>
                                    <dd class="col-7">{{ $invoice->client_phone ?: 'Not set' }}</dd>
                                </dl>
                            </div>
                        </div>

                        @if($invoice->notes)
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white">
                                    <h3 class="card-title font-weight-bold mb-0">Notes</h3>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $invoice->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
