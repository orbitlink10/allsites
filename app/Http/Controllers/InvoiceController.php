<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q'));

        $query = Invoice::with('items')->latest();

        if ($status && array_key_exists($status, Invoice::STATUSES)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('client_email', 'like', "%{$search}%")
                    ->orWhere('client_phone', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(12)->withQueryString();
        $summaryInvoices = Invoice::with('items')->get();
        $summary = [
            'count' => $summaryInvoices->count(),
            'pending' => $summaryInvoices->where('status', 'pending')->count(),
            'paid' => $summaryInvoices->where('status', 'paid')->count(),
            'overdue' => $summaryInvoices->where('status', 'overdue')->count(),
            'outstanding' => $summaryInvoices
                ->whereIn('status', ['pending', 'overdue'])
                ->sum(fn (Invoice $invoice) => $invoice->total()),
        ];

        return view('invoices.index', [
            'invoices' => $invoices,
            'summary' => $summary,
            'statuses' => Invoice::STATUSES,
        ]);
    }

    public function create()
    {
        $invoice = new Invoice([
            'currency' => 'KES',
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);

        $invoice->setRelation('items', collect([
            new InvoiceItem(['description' => '', 'quantity' => 1, 'unit_price' => 0, 'amount' => 0]),
        ]));

        return view('invoices.create', $this->formData($invoice));
    }

    public function store(Request $request)
    {
        $validated = $this->validateInvoice($request);

        $invoice = DB::transaction(function () use ($validated) {
            $items = $validated['items'];
            unset($validated['items']);

            $invoice = Invoice::create($validated);
            $this->syncItems($invoice, $items);

            return $invoice;
        });

        return redirect()
            ->route('invoices.edit', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'user');

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items', 'user');

        return view('invoices.edit', $this->formData($invoice));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $this->validateInvoice($request);

        DB::transaction(function () use ($invoice, $validated) {
            $items = $validated['items'];
            unset($validated['items']);

            $invoice->update($validated);
            $this->syncItems($invoice, $items);
        });

        return redirect()
            ->route('invoices.edit', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function open(string $slug)
    {
        $invoice = Invoice::with('items', 'user')->where('slug', $slug)->firstOrFail();

        return view('invoices.open', compact('invoice'));
    }

    public function download(int $id)
    {
        $invoice = Invoice::with('items', 'user')->findOrFail($id);

        return $this->downloadPdf($invoice);
    }

    public function downloadPublic(string $slug)
    {
        $invoice = Invoice::with('items', 'user')->where('slug', $slug)->firstOrFail();

        return $this->downloadPdf($invoice);
    }

    private function formData(Invoice $invoice): array
    {
        return [
            'invoice' => $invoice,
            'statuses' => Invoice::STATUSES,
            'currencies' => ['KES', 'USD', 'EUR', 'GBP'],
        ];
    }

    private function validateInvoice(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'client_address' => ['nullable', 'string', 'max:2000'],
            'currency' => ['required', 'string', 'max:10'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(Invoice::STATUSES))],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.description' => ['required', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
        ]);

        $validated['currency'] = strtoupper($validated['currency']);

        return $validated;
    }

    private function syncItems(Invoice $invoice, array $items): void
    {
        $keptIds = [];

        foreach ($items as $item) {
            $quantity = round((float) $item['quantity'], 2);
            $unitPrice = round((float) $item['unit_price'], 2);
            $data = [
                'description' => $item['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'amount' => round($quantity * $unitPrice, 2),
            ];

            if (! empty($item['id'])) {
                $invoiceItem = $invoice->items()->whereKey($item['id'])->first();
                if ($invoiceItem) {
                    $invoiceItem->update($data);
                    $keptIds[] = $invoiceItem->id;
                    continue;
                }
            }

            $keptIds[] = $invoice->items()->create($data)->id;
        }

        $invoice->items()
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))
            ->delete();
    }

    private function downloadPdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))->setPaper('a4');

        return $pdf->download('invoice-' . ($invoice->invoice_number ?: $invoice->id) . '.pdf');
    }
}
