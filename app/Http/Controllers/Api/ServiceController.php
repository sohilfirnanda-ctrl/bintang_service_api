<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /** GET /api/services?search=oli&per_page=5 */
    public function index(Request $request): JsonResponse
    {
        $services = Service::query()
            ->when($request->query('search'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($this->perPage($request));

        return ApiResponse::paginated($services, ServiceResource::class, 'Daftar layanan berhasil diambil.');
    }

    /** GET /api/services/{service} */
    public function show(Service $service): JsonResponse
    {
        return ApiResponse::success(new ServiceResource($service), 'Detail layanan berhasil diambil.');
    }

    /** POST /api/services (admin) */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        return ApiResponse::success(new ServiceResource($service->fresh()), 'Layanan berhasil ditambahkan.', 201);
    }

    /** PUT /api/services/{service} (admin) */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return ApiResponse::success(new ServiceResource($service), 'Layanan berhasil diperbarui.');
    }

    /** DELETE /api/services/{service} (admin) */
    public function destroy(Service $service): JsonResponse
    {
        if ($service->bookingDetails()->exists()) {
            return ApiResponse::error(
                'Layanan sudah pernah dipesan sehingga tidak dapat dihapus. Nonaktifkan layanan (is_active=false) sebagai gantinya.',
                409
            );
        }

        $service->delete();

        return ApiResponse::success(null, 'Layanan berhasil dihapus.');
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->query('per_page', 10), 1), 50);
    }
}