<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item) {
            $quantity = max((float) ($item->quantity ?: 1), 0);
            $unitPrice = (float) ($item->unit_price ?? $item->amount ?? 0);

            $item->quantity = $quantity ?: 1;
            $item->unit_price = round($unitPrice, 2);
            $item->amount = round($item->quantity * $item->unit_price, 2);
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function lineTotal(): float
    {
        return round((float) ($this->amount ?? ((float) $this->quantity * (float) $this->unit_price)), 2);
    }
}
