<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

    protected $fillable = [
        'booking_code',
        'customer_id',
        'motor_type',
        'plate_number',
        'booking_date',
        'booking_time',
        'complaint',
        'status',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date:Y-m-d',
            'total_price' => 'integer',
        ];
    }

    public static function generateCode(): string
    {
        return 'BS-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
    }

    /** Booking masih boleh diubah jadwal/datanya oleh pelanggan. */
    public function isEditable(): bool
    {
        return $this->status === 'pending';
    }

    /** Booking masih boleh dibatalkan oleh pelanggan. */
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'], true);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }
}