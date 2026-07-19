<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address',
        'delivery_type',
        'payment_method',
        'subtotal',
        'delivery_price',
        'total',
        'status',
        'comment',
        'stock_returned_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_price' => 'decimal:2',
            'total' => 'decimal:2',
            'stock_returned_at' => 'datetime',
        ];
    }

    public function isCancelled(): bool
    {
        return $this->status === \App\Support\OrderStatus::CANCELLED;
    }

    public function statusLabel(): string
    {
        return \App\Support\OrderStatus::label($this->status);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
