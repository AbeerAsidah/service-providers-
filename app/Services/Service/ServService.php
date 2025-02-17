<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Requests\Api\Service\UpdateServiceRequest;
use Illuminate\Support\Facades\Storage;

class ServService
{
    /**
     * Get all services, with optional trash filtering.
     */
    public function getAll($trashOnly = false, $paginate = false, $limit = 10)
    {
        $servicesQuery = Service::query();

        // Apply soft delete filter if requested
        if ($trashOnly) {
            $servicesQuery->onlyTrashed();
        }

        $servicesQuery->orderByDesc($trashOnly ? 'deleted_at' : 'created_at');
        
        // Load the associated category and provider relationships
        $servicesQuery->with(['category', 'provider', 'reviews']);

        // Paginate the results if needed
        if ($paginate) {
            return $servicesQuery->paginate($limit);
        }

        return $servicesQuery->get();
    }

    /**
     * Store a new service.
     */
    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();

        // Handle file upload if exists
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('services/images', 'public');
        }

        // Store the service data
        $data['name'] = [
            'ar' => $data['ar_name'],
            'en' => $data['en_name'],
        ];

        $service = Service::create($data);

        return new ServiceResource($service);
    }

    /**
     * Update an existing service.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();

        // Handle file upload if exists
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('services/images', 'public');

            // Delete old image if exists
            if (Storage::exists("public/$service->image")) {
                Storage::delete("public/$service->image");
            }
        }

        // Update service name in different languages
        if (isset($data['ar_name'])) {
            $data['name']['ar'] = $data['ar_name'];
        }
        if (isset($data['en_name'])) {
            $data['name']['en'] = $data['en_name'];
        }

        // Update the service data
        $service->update($data);

        return new ServiceResource($service);
    }

    /**
     * Soft delete a service.
     */
    public function delete($id, $force = null)
    {
        $service = Service::findOrFail($id);

        if ($force) {
            $service->forceDelete();
        } else {
            $service->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ], 200);
    }

    /**
     * Restore a soft-deleted service.
     */
    public function restore(string|int $id)
    {
        $service = Service::withTrashed()->find($id);

        if ($service && $service->trashed()) {
            $service->restore();
            return response()->json([
                'success' => true,
                'message' => 'Service restored successfully.',
            ], 200);
        }

        throw new \Exception("Service not found", 404);
    }

    /**
     * Get a specific service by ID.
     */
    public function show(int $id)
    {
        // Retrieve the service along with related category and provider data
        $service = Service::with(['category', 'provider'])->findOrFail($id);

        return new ServiceResource($service);
    }

    /**
     * Get all services (used for listing).
     */
    public function getAllServices()
    {
        $services = Service::with(['category', 'provider'])
                           ->orderByDesc('created_at')
                           ->get();

        return ServiceResource::collection($services);
    }

    /**
     * Get the service categories (for filtering).
     */
    public function getCategories()
    {
        return Category::all(); // Return all categories to be used for filtering services
    }
    
    /**
     * Get manufacture years for services (if applicable).
     */
    public function getManufactureYears($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        return $service->manufacture_years ?? [];
    }
}
