<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /**
     * Show the form to add items to the specified invoice.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\View\View
     */
    public function create(Invoice $invoice)
    {
        $invoice->load('items');

        return view('invoices.items.create', compact('invoice'));
    }

    /**
     * Store a newly created item for the specified invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Invoice $invoice)
    {
        // Validate the request
        $request->validate([
            'description' => 'required|string|max:1000',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Create the invoice item
        $invoice->items()->create([
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity'),
            'unit_price' => $request->input('unit_price'),
            'amount' => (float) $request->input('quantity') * (float) $request->input('unit_price'),
        ]);

        // Redirect back to the create items page with a success message
        return redirect()->route('invoice.items.create', $invoice->id)
            ->with('success', 'Item added successfully.');
    }


    public function destroy(Invoice $invoice, $itemId)
{
    $invoice->items()->where('id', $itemId)->delete();

    return redirect()->back()
        ->with('success', 'Item deleted successfully.');
}

}
