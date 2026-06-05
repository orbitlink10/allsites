<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'Draft',
        'pending' => 'Pending',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'user_id',
        'invoice_number',
        'name',
        'slug',
        'currency',
        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'notes',
        'terms',
        'due_date',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    protected $appends = [
        'total_amount',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            $invoice->currency = strtoupper($invoice->currency ?: 'KES');
            $invoice->status = $invoice->status ?: 'pending';

            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }

            if (empty($invoice->slug)) {
                $invoice->slug = static::generateSlug($invoice->invoice_number);
            }
        });

        static::saving(function (Invoice $invoice) {
            $invoice->currency = strtoupper($invoice->currency ?: 'KES');

            if ($invoice->status === 'paid' && empty($invoice->paid_at)) {
                $invoice->paid_at = now();
            }

            if ($invoice->status !== 'paid') {
                $invoice->paid_at = null;
            }
        });
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function total(): float
    {
        if ($this->relationLoaded('items')) {
            return round((float) $this->items->sum(fn (InvoiceItem $item) => $item->lineTotal()), 2);
        }

        return round((float) $this->items()->sum('amount'), 2);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->total();
    }

    public function formattedTotal(): string
    {
        return $this->formatMoney($this->total());
    }

    public function formatMoney(float|int|string|null $amount): string
    {
        return trim(($this->currency ?: 'KES') . ' ' . number_format((float) $amount, 2));
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'secondary',
            'draft' => 'dark',
            default => 'warning',
        };
    }

    public function isPayable(): bool
    {
        return in_array($this->status, ['pending', 'overdue'], true) && $this->total() > 0;
    }

    public function markAsPaid(): void
    {
        $this->forceFill([
            'status' => 'paid',
            'paid_at' => now(),
        ])->save();
    }

    private static function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(3));
        } while (static::where('invoice_number', $number)->exists());

        return $number;
    }

    private static function generateSlug(string $invoiceNumber): string
    {
        do {
            $slug = Str::slug($invoiceNumber) . '-' . Str::lower(Str::random(6));
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }
}
