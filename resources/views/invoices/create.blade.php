@extends('layouts.appbar')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="page-title">Create Invoice</h1>
                    <p class="text-muted mb-0">Prepare a customer-ready invoice with line items and payment details.</p>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @include('invoices._form', [
                    'action' => route('invoices.store'),
                    'method' => 'POST',
                ])
            </div>
        </section>
    </div>
@endsection
