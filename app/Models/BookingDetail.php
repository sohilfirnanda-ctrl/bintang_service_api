<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDetail extends Model
{
    protected $fillable = [
        'booking_id',
        'service_id',
        'service_name',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}