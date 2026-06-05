@php
    $storedItems = $invoice->items->map(function ($item) {
        return [
            'id' => $item->id,
            'description' => $item->description,
            'quantity' => $item->quantity ?: 1,
            'unit_price' => $item->unit_price ?? $item->amount ?? 0,
        ];
    })->values()->toArray();

    $formItems = old('items', $storedItems ?: [[
        'id' => null,
        'description' => '',
        'quantity' => 1,
        'unit_price' => 0,
    ]]);
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please check the invoice details.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST" id="invoice-form">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold mb-0">Invoice Details</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Invoice title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $invoice->name) }}" placeholder="e.g. Welding service invoice" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="currency">Currency <span class="text-danger">*</span></label>
                        <select id="currency" name="currency" class="form-control" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}" {{ old('currency', $invoice->currency ?: 'KES') === $currency ? 'selected' : '' }}>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $invoice->status ?: 'pending') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="client_name">Client name</label>
                        <input type="text" class="form-control" id="client_name" name="client_name" value="{{ old('client_name', $invoice->client_name) }}" placeholder="Customer or company name">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="client_email">Client email</label>
                        <input type="email" class="form-control" id="client_email" name="client_email" value="{{ old('client_email', $invoice->client_email) }}" placeholder="client@example.com">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="client_phone">Client phone</label>
                        <input type="text" class="form-control" id="client_phone" name="client_phone" value="{{ old('client_phone', $invoice->client_phone) }}" placeholder="2547...">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        <label for="client_address">Client address</label>
                        <textarea class="form-control" id="client_address" name="client_address" rows="3" placeholder="Billing address">{{ old('client_address', $invoice->client_address) }}</textarea>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="due_date">Due date</label>
                        <input type="date" id="due_date" name="due_date" class="form-control" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}">
                    </div>
                    @if($invoice->exists)
                        <div class="small text-muted mt-4">
                            <div><strong>Invoice no:</strong> {{ $invoice->invoice_number }}</div>
                            <div><strong>Public link:</strong> <a href="{{ route('invoices.open', $invoice->slug) }}" target="_blank">{{ route('invoices.open', $invoice->slug) }}</a></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">Line Items</h3>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-invoice-item">
                <i class="fas fa-plus"></i> Add item
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="invoice-items-table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 48%;">Description</th>
                            <th style="width: 14%;">Qty</th>
                            <th style="width: 18%;">Rate</th>
                            <th style="width: 16%;" class="text-right">Line total</th>
                            <th style="width: 4%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formItems as $index => $item)
                            <tr class="invoice-item-row">
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
                                    <input type="text" class="form-control item-description" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" placeholder="Item or service description" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="0.01" step="0.01" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control item-rate" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}" min="0" step="0.01" required>
                                </td>
                                <td class="text-right align-middle">
                                    <span class="line-total">0.00</span>
                                </td>
                                <td class="text-right align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-invoice-item" aria-label="Remove item">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total</th>
                            <th class="text-right"><span id="invoice-total">0.00</span></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold mb-0">Notes</h3>
        </div>
        <div class="card-body">
            <div class="form-group mb-0">
                <label for="notes">Terms or customer note</label>
                <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Payment terms, delivery notes, or any extra context">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-5">
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $invoice->exists ? 'Save Invoice' : 'Create Invoice' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    (function () {
        const table = document.getElementById('invoice-items-table');
        const addButton = document.getElementById('add-invoice-item');
        const totalEl = document.getElementById('invoice-total');

        function money(value) {
            return Number(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function reindexRows() {
            table.querySelectorAll('tbody tr').forEach((row, index) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                });
            });
        }

        function updateTotals() {
            let total = 0;
            table.querySelectorAll('tbody tr').forEach((row) => {
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
                const lineTotal = quantity * rate;
                row.querySelector('.line-total').textContent = money(lineTotal);
                total += lineTotal;
            });
            totalEl.textContent = money(total);
        }

        function bindRow(row) {
            row.querySelectorAll('.item-quantity, .item-rate').forEach((input) => {
                input.addEventListener('input', updateTotals);
            });
            row.querySelector('.remove-invoice-item').addEventListener('click', function () {
                const rows = table.querySelectorAll('tbody tr');
                if (rows.length === 1) {
                    row.querySelector('.item-description').value = '';
                    row.querySelector('.item-quantity').value = '1';
                    row.querySelector('.item-rate').value = '0';
                } else {
                    row.remove();
                }
                reindexRows();
                updateTotals();
            });
        }

        addButton.addEventListener('click', function () {
            const source = table.querySelector('tbody tr');
            const clone = source.cloneNode(true);
            clone.querySelectorAll('input').forEach((input) => {
                input.value = input.type === 'hidden' ? '' : '';
            });
            clone.querySelector('.item-quantity').value = '1';
            clone.querySelector('.item-rate').value = '0';
            clone.querySelector('.line-total').textContent = '0.00';
            table.querySelector('tbody').appendChild(clone);
            bindRow(clone);
            reindexRows();
            updateTotals();
        });

        table.querySelectorAll('tbody tr').forEach(bindRow);
        updateTotals();
    })();
</script>
@endpush
