<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\CustomerResource;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** GET /api/admin/customers?search=nurul */
    public function customers(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->where('role', 'customer')
            ->withCount('bookings')
            ->when($request->query('search'), fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(min(max((int) $request->query('per_page', 10), 1), 50));

        return ApiResponse::paginated($customers, CustomerResource::class, 'Daftar pelanggan berhasil diambil.');
    }

    /** GET /api/admin/customers/{customer} */
    public function showCustomer(Customer $customer): JsonResponse
    {
        return ApiResponse::success(
            new CustomerResource($customer->loadCount('bookings')),
            'Detail pelanggan berhasil diambil.'
        );
    }

    /** GET /api/admin/customers/{customer}/bookings */
    public function customerBookings(Request $request, Customer $customer): JsonResponse
    {
        $bookings = $customer->bookings()
            ->with('details')
            ->latest()
            ->paginate(min(max((int) $request->query('per_page', 10), 1), 50));

        return ApiResponse::paginated($bookings, BookingResource::class, "Riwayat booking {$customer->name} berhasil diambil.");
    }

    /** GET /api/admin/summary */
    public function summary(): JsonResponse
    {
        $byStatus = Booking::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCounts = collect(Booking::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => (int) ($byStatus[$status] ?? 0)]);

        return ApiResponse::success([
            'total_customers' => Customer::where('role', 'customer')->count(),
            'total_services' => Service::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'total_bookings' => Booking::count(),
            'bookings_by_status' => $statusCounts,
            'total_revenue' => (int) Booking::where('status', 'completed')->sum('total_price'),
        ], 'Ringkasan data bengkel berhasil diambil.');
    }
}