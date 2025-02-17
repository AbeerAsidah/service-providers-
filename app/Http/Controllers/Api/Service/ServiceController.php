<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Services\Service\ServService;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Requests\Api\Service\UpdateServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    protected ServService $service;

    public function __construct(ServService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the services.
     */
    public function index(Request $request): JsonResponse
    {
        $services = $this->service->getAll(
            $request->query('trashOnly', false), 
            $request->query('paginate', false), 
            $request->query('limit', 10)
        );
        
        return response()->json($services, 200);
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->service->store($request);
        return response()->json($service, 201);
    }

    /**
     * Display the specified service.
     */
    public function show(int $id): JsonResponse
    {
        $service = $this->service->show($id);
        return response()->json($service, 200);
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $updatedService = $this->service->update($request, $service);
        return response()->json($updatedService, 200);
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $force = $request->query('force', false);
        $this->service->delete($id, $force);
        return response()->json(['message' => 'Service deleted successfully'], 200);
    }

    /**
     * Restore a soft-deleted service.
     */
    public function restore(int $id): JsonResponse
    {
        $this->service->restore($id);
        return response()->json(['message' => 'Service restored successfully'], 200);
    }
}
