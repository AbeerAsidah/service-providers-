<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Category\StoreCategoryRequest;
use App\Http\Requests\Api\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {
    }

    public function index(Request $request)
    {
        try {
            $trashOnly = $request->input('trash', false);
            $categories = $this->categoryService->getAll($trashOnly);
            return success(['categories' => $categories]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->categoryService->store($request);
            return success(['category' => $category]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $updatedCategory = $this->categoryService->update($request, $category);
            return success(['category' => $updatedCategory]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function delete($id, $force = null)
    {
        try {
            $this->categoryService->delete($id, $force);
            return success(['message' => __('messages.category_deleted')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function restore($id)
    {
        try {
           $this->categoryService->restore($id);
           return success(['message' => __('messages.category_restored')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryService->show($id);
            return success(['category' => $category]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getAllForUser()
    {
        try {
            $categories = $this->categoryService->getAllCategories();
            return success(['categories' => $categories]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getServicesByCategory($categoryId)
    {
        try {
            $services = $this->categoryService->getServicesByCategory($categoryId);
            return success(['services' => $services]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }
}
