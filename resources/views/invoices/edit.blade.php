@extends('layouts.appbar')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="page-title">Edit Invoice</h1>
                    <p class="text-muted mb-0">{{ $invoice->invoice_number }} &middot; {{ $invoice->formattedTotal() }}</p>
                </div>
                <div class="mt-3 mt-md-0">
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
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @include('invoices._form', [
                    'action' => route('invoices.update', $invoice),
                    'method' => 'PUT',
                ])
            </div>
        </section>
    </div>
@endsection
