<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Http\Resources\BookingDetailResource;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Service;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /** GET /api/bookings?status=pending  (pelanggan: miliknya sendiri, admin: semua) */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $bookings = Booking::query()
            ->with(['details', 'customer'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('customer_id', $user->id))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(min(max((int) $request->query('per_page', 10), 1), 50));

        return ApiResponse::paginated($bookings, BookingResource::class, 'Riwayat booking berhasil diambil.');
    }

    /** POST /api/bookings */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $services = Service::whereIn('id', $data['service_ids'])->get();

        $booking = DB::transaction(function () use ($request, $data, $services) {
            $booking = Booking::create([
                'booking_code' => Booking::generateCode(),
                'customer_id' => $request->user()->id,
                'motor_type' => $data['motor_type'],
                'plate_number' => strtoupper($data['plate_number']),
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'complaint' => $data['complaint'] ?? null,
                'status' => 'pending',
                'total_price' => $services->sum('price'),
            ]);

            foreach ($services as $service) {
                $booking->details()->create([
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'price' => $service->price,
                ]);
            }

            return $booking;
        });

        return ApiResponse::success(
            new BookingResource($booking->load(['details', 'customer'])),
            'Booking berhasil dibuat.',
            201
        );
    }

    /** GET /api/bookings/{booking} */
    public function show(Request $request, Booking $booking): JsonResponse
    {
        $this->ensureOwner($request, $booking);

        return ApiResponse::success(
            new BookingResource($booking->load(['details', 'customer'])),
            'Detail booking berhasil diambil.'
        );
    }

    /** PUT /api/bookings/{booking}  (ubah data kendaraan / jadwal, hanya saat pending) */
    public function update(UpdateBookingRequest $request, Booking $booking): JsonResponse
    {
        $this->ensureOwner($request, $booking);

        if (! $booking->isEditable()) {
            return ApiResponse::error('Booking hanya dapat diubah saat status masih pending.', 422);
        }

        $data = $request->validated();
        if (isset($data['plate_number'])) {
            $data['plate_number'] = strtoupper($data['plate_number']);
        }

        $booking->update($data);

        return ApiResponse::success(
            new BookingResource($booking->load(['details', 'customer'])),
            'Booking berhasil diperbarui.'
        );
    }

    /** PATCH /api/bookings/{booking}/cancel */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $this->ensureOwner($request, $booking);

        if (! $booking->isCancellable()) {
            return ApiResponse::error("Booking berstatus {$booking->status} tidak dapat dibatalkan.", 422);
        }

        $booking->update(['status' => 'cancelled']);

        return ApiResponse::success(new BookingResource($booking), 'Booking berhasil dibatalkan.');
    }

    /** GET /api/bookings/{booking}/details */
    public function details(Request $request, Booking $booking): JsonResponse
    {
        $this->ensureOwner($request, $booking);

        return ApiResponse::success(
            BookingDetailResource::collection($booking->details)->resolve(),
            'Rincian layanan booking berhasil diambil.'
        );
    }

    /** PATCH /api/bookings/{booking}/status (admin) */
    public function updateStatus(UpdateBookingStatusRequest $request, Booking $booking): JsonResponse
    {
        $booking->update(['status' => $request->validated('status')]);

        return ApiResponse::success(
            new BookingResource($booking->load(['details', 'customer'])),
            'Status booking berhasil diperbarui.'
        );
    }

    /** Pelanggan hanya boleh mengakses booking miliknya sendiri; admin boleh semua. */
    private function ensureOwner(Request $request, Booking $booking): void
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || (int) $booking->customer_id === (int) $user->id,
            403,
            'Anda tidak berhak mengakses booking ini.'
        );
    }
}