<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Services\Service\ServService;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Requests\Api\Service\UpdateServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Requests\Api\Service\ChangeServiceStatusRequest;
use Illuminate\Support\Facades\Log;


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
        try {
            $trashOnly = $request->input('trashOnly',false);
            $paginate = $request->input('paginate', false);
            $limit = $request->input('limit', 10);
            $status = $request->input('status');


            Log::info('service index request received', [
                'trashOnly' => $trashOnly,
                'status' => $status,
                'paginate' => $paginate,
                'limit' => $limit
            ]);

            $services = $this->service->getAll(
                $trashOnly,
                $paginate,
                $limit
            );
            return success(['services' => $services]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        try {
            $service = $this->service->store($request);
            return success(['service' => $service], 201);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    /**
     * Display the specified service.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $service = $this->service->show($id);
            return success(['service' => $service]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 404);
        }
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        try {
            $updatedService = $this->service->update($request, $service);
            return success(['service' => $updatedService]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(int $id, $force = null): JsonResponse
    {
        try {
            $this->service->delete($id, $force);
            return success(['message' =>  __('messages.service_delete_success')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    /**
     * Restore a soft-deleted service.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $this->service->restore($id);
            return success(['message' =>  __('messages.service_restore_success')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }


    public function changeStatus(ChangeServiceStatusRequest $request , int $id): JsonResponse
    {
        try {
          $service =  $this->service->changeStatus($request, $id);
            return success(['service' => $service,'message' =>  __('messages.status_updated')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function searchServices(Request $request)
    {
        try {
            $searchTerm = $request->input('search_term');
            $paginate = $request->input('paginate', false);
            $limit = $request->input('limit', 10);

            $services = $this->service->searchServices($searchTerm, $paginate, $limit);

            return success([
                'services' => $services,
                'message' => __('messages.services_found')
            ]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }



}
