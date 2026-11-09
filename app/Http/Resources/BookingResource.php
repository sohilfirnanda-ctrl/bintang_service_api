<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'motor_type' => $this->motor_type,
            'plate_number' => $this->plate_number,
            'booking_date' => $this->booking_date?->format('Y-m-d'),
            'booking_time' => substr((string) $this->booking_time, 0, 5),
            'complaint' => $this->complaint,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'details' => BookingDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}