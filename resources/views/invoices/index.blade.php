@extends('layouts.appbar')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="page-title">Invoices</h1>
                    <p class="text-muted mb-0">Create, send, track, and reconcile customer invoices.</p>
                </div>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary mt-3 mt-md-0">
                    <i class="fas fa-plus"></i> New Invoice
                </a>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $summary['count'] }}</h3>
                                <p>Total invoices</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $summary['pending'] }}</h3>
                                <p>Pending</p>
                            </div>
                            <div class="icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $summary['paid'] }}</h3>
                                <p>Paid</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($summary['outstanding'], 0) }}</h3>
                                <p>Outstanding value</p>
                            </div>
                            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <form method="GET" action="{{ route('invoices.index') }}" class="row align-items-end">
                            <div class="col-lg-5">
                                <label for="q">Search</label>
                                <input type="search" id="q" name="q" class="form-control" value="{{ request('q') }}" placeholder="Invoice no, title, client, email, phone">
                            </div>
                            <div class="col-lg-3 mt-3 mt-lg-0">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">All statuses</option>
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mt-3 mt-lg-0 text-lg-right">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Client</th>
                                        <th>Due</th>
                                        <th>Status</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <strong>{{ $invoice->invoice_number }}</strong>
                                                <div class="small text-muted">{{ $invoice->name }}</div>
                                            </td>
                                            <td>
                                                {{ $invoice->client_name ?: 'No client name' }}
                                                @if($invoice->client_email)
                                                    <div class="small text-muted">{{ $invoice->client_email }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                {{ optional($invoice->due_date)->format('M d, Y') ?: 'No due date' }}
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $invoice->statusClass() }}">{{ $invoice->statusLabel() }}</span>
                                            </td>
                                            <td class="text-right font-weight-bold">{{ $invoice->formattedTotal() }}</td>
                                            <td class="text-right text-nowrap">
                                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('invoices.open', $invoice->slug) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Public invoice">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-outline-secondary" title="Download PDF">
                                                    <i class="fas fa-file-download"></i>
                                                </a>
                                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this invoice permanently?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                No invoices found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-center">
                        {{ $invoices->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
