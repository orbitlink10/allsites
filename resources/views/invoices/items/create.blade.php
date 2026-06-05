@extends('layouts.appbar')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="page-title">Add Line Item</h1>
                    <p class="text-muted mb-0">{{ $invoice->invoice_number }} &middot; {{ $invoice->name }}</p>
                </div>
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary mt-3 mt-md-0">
                    <i class="fas fa-arrow-left"></i> Back to Invoice
                </a>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please check the item details.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h3 class="card-title font-weight-bold mb-0">New Item</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('invoice.items.store', $invoice) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="0.01" step="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="unit_price">Rate</label>
                                        <input type="number" class="form-control" id="unit_price" name="unit_price" value="{{ old('unit_price', 0) }}" min="0" step="0.01" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold mb-0">Current Items</h3>
                                <strong>{{ $invoice->formattedTotal() }}</strong>
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
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoice->items as $item)
                                                <tr>
                                                    <td>{{ $item->description }}</td>
                                                    <td class="text-right">{{ number_format((float) $item->quantity, 2) }}</td>
                                                    <td class="text-right">{{ $invoice->formatMoney($item->unit_price) }}</td>
                                                    <td class="text-right">{{ $invoice->formatMoney($item->lineTotal()) }}</td>
                                                    <td class="text-right">
                                                        <form action="{{ route('invoice.items.destroy', [$invoice, $item->id]) }}" method="POST" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this line item?')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">No line items yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
