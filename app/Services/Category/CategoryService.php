<?php

namespace App\Services\Category;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ServiceResource;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\Category\StoreCategoryRequest;
use App\Http\Requests\Api\Category\UpdateCategoryRequest;



class CategoryService
{
    public function getAll($trashOnly = false)
    {
        $categoriesQuery = Category::query();
    
        if ($trashOnly) {
            $categoriesQuery->onlyTrashed();
        }
    
        $categoriesQuery->orderByDesc($trashOnly ? 'deleted_at' : 'created_at');
        
        // if ($paginate) {
        //     return $companiesQuery->paginate($limit);
        // }
    
        return $categoriesQuery->get();
    }
    

    public function store(StoreCategoryRequest $request)
    {
       
        $data = $request->validated();

    
        $data['name'] = [
            'ar' => $data['ar_name'],
            'en' => $data['en_name'],
        ];
        $data['description'] = [
            'ar' => $data['ar_description'],
            'en' => $data['en_description'],
        ];

        $category = Category::create($data);
        return $category;
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

       
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

        $category->update($data);
        return $category;
    }

    public function delete($id, $force = null)
    {
        if ($force) {
            $category = Category::onlyTrashed()->findOrFail($id);        
            $category->forceDelete();
        } else {
            $category = Category::findOrFail($id); 
            $category->delete();  
           }

   }

    public function restore(string|int $id)
    {
        $category = Category::withTrashed()->find($id);

        if ($category && $category->trashed()) {
            $category->restore();
            
            return response()->json([
                'success' => true,
            ], 200);  
              }

        throw new \Exception("Category not found", 404);
    }


    public function show(int $id)
    {
        $category = Category::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return new CategoryResource($category);
        }
    

        if (auth()->user()->hasRole(Constants::ADMIN_ROLE)) {
            return $category;
        }
    
        return new CategoryResource($category);
    }

    

    //user

    public function getServicesByCategory($CategoryId)
    {
        $category = Category::findOrFail($CategoryId);

        // $Services = $Category->Services()->paginate(config('app.pagination_limit'));
        $services = $category->services()->get();

        return ServiceResource::collection($services);
    }

    public function getAllCategories()
    {
        $categoriesQuery = Category::query();

        $categoriesQuery->orderByDesc('created_at');
        // $companies = $companiesQuery->paginate(config('app.pagination_limit'));
        $categories = $categoriesQuery->get();

        return CategoryResource::collection($categories);
    }



}
