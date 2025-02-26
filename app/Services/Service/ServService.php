<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Requests\Api\Service\UpdateServiceRequest;
use Illuminate\Support\Facades\Storage;
use App\Constants\Constants;
use App\Http\Requests\Api\Service\ChangeServiceStatusRequest;


class ServService
{
    /**
     * Get all services, with optional trash filtering.
     */
    public function getAll($trashOnly = false, $paginate = false, $limit = 10)
    {


        $servicesQuery = Service::query();
        $user = auth()->user();
    
       
        if ($user && $user->hasRole(Constants::SERVICE_PROVIDER_ROLE)) {
            $query->where('service_provider_id', $user->id);
        }

        // Apply soft delete filter if requested
        if ($trashOnly) {
            $servicesQuery->onlyTrashed();
        }

        $servicesQuery->orderByDesc($trashOnly ? 'deleted_at' : 'created_at');
        
        // Load the associated category and provider relationships
        $servicesQuery->with(['category', 'provider', 'reviews']);

        $services = $paginate ? $servicesQuery->paginate($limit) : $servicesQuery->get();

        
        // If the logged-in user is a normal user, return as ServiceResource
        if (!$user || $user->hasRole(Constants::USER_ROLE)) {
            return ServiceResource::collection($services);
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

        if (auth()->user()->hasRole(Constants::ADMIN_ROLE)) {
            if ($request->has('service_provider_id')) {
                $data['service_provider_id'] = $request->input('service_provider_id');
            } 
        } elseif (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE)) {
            $data['service_provider_id'] = auth()->id();
        } 

        // Store the service data
        $data['name'] = [
            'ar' => $data['ar_name'],
            'en' => $data['en_name'],
        ];
        $data['description'] = [
            'ar' => $data['ar_description'],
            'en' => $data['en_description'],
        ];

        $service = Service::create($data);
        $service->refresh(); 
        return $service;
    }

    /**
     * Update an existing service.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();

        if (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE) && $service->service_provider_id !== auth()->id()) {
            return error(__('messages.unauthorized_action'), [], 403);
        }

        // Handle file upload if exists
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('services/images', 'public');

            // Delete old image if exists
            if (Storage::exists("public/$service->image")) {
                Storage::delete("public/$service->image");
            }
        }
        if (auth()->user()->hasRole(Constants::ADMIN_ROLE)) {
            if ($request->has('service_provider_id')) {
                $data['service_provider_id'] = $request->input('service_provider_id');
            } 
        }

        // Update service name in different languages
        if (isset($data['ar_name'])) {
            $data['name']['ar'] = $data['ar_name'];
        }
        if (isset($data['en_name'])) {
            $data['name']['en'] = $data['en_name'];
        }
        if (isset($data['ar_description'])) {
            $data['description']['ar'] = $data['ar_description'];
        }
        if (isset($data['en_description'])) {
            $data['description']['en'] = $data['en_description'];
        }

        // Update the service data
        $service->update($data);
        $service->refresh(); 

        return $service;
    }

    /**
     * Soft delete a service.
     */
    public function delete($id, $force = null)
    {

        $service = Service::findOrFail($id);

        if (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE) && $service->service_provider_id !== auth()->id()) {
            return error(__('messages.unauthorized_action'), [], 403);
        }

        if ($force) {
            $service->forceDelete();
        } else {
            $service->delete();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    /**
     * Restore a soft-deleted service.
     */
    public function restore(string|int $id)
    {
       
        $service = Service::withTrashed()->find($id);
        if (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE) && $service->service_provider_id !== auth()->id()) {
            return error(__('messages.unauthorized_action'), [], 403);
        }
        if ($service && $service->trashed()) {
            $service->restore();
            return response()->json([
                'success' => true,
            ], 200);
        }

        throw new \Exception("Service not found", 404);
    }

    /**
     * Get a specific service by ID.
     */
    public function show(int $id)
    {
        $service = Service::with(['category', 'provider', 'reviews'])->findOrFail($id);
    
        $user = auth()->user();
    
        if (!$user) {
            return new ServiceResource($service);
        }
    
        if ($user->hasRole(Constants::SERVICE_PROVIDER_ROLE) && $service->service_provider_id !== $user->id) {
            return error(__('messages.unauthorized_action'), [], 403);
        }
    
        if ($user->hasRole(Constants::USER_ROLE)) {
            return new ServiceResource($service);
        }
    
        return $service;
    }
    
    /**
     * Change the status of a service.
     */
    public function changeStatus(ChangeServiceStatusRequest $request, int $id)
    {
        $service = Service::with(['category', 'provider', 'reviews'])->findOrFail($id);

        if (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE) && $service->service_provider_id !== auth()->id()) {
            return error(__('messages.unauthorized_action'), [], 403);
        }

        $service->update(['status' => $request->status]);

        return $service;
    }

   


        public function searchServices($searchTerm, $paginate = false, $limit = 10)
    {
        $query = Service::query()->with(['category', 'provider']);

        $searchTerm = mb_strtolower($searchTerm, 'UTF-8');

        $query->where(function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(name->"$.ar") LIKE ?', ["%$searchTerm%"])
            ->orWhereRaw('LOWER(name->"$.en") LIKE ?', ["%$searchTerm%"])
            ->orWhereRaw('LOWER(description->"$.ar") LIKE ?', ["%$searchTerm%"])
            ->orWhereRaw('LOWER(description->"$.en") LIKE ?', ["%$searchTerm%"]);
        });

        $query->orWhereHas('category', function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%$searchTerm%"]);
        });

        $query->orWhereHas('provider', function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%$searchTerm%"])
            ->orWhere('phone', 'LIKE', "%$searchTerm%");
        });

        if (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE)) {
            $query->where('service_provider_id', auth()->id());
        }

        $query->orderByDesc('created_at');

        if ($paginate) {
            return $query->paginate($limit);
        }

        return $query->get();
    }



        
        
}
