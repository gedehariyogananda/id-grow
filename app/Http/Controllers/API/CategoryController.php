<?php

namespace App\Http\Controllers\API;

use App\Helper\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            $users = $this->categoryService->get(request(Category::$allowedParams));
            return ApiResponseHelper::paginated($users);
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryService->find($id);
            if (!$category) {
                return ApiResponseHelper::error('Category not found', 404);
            }
            return ApiResponseHelper::success($category, 'Category details retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        $category = $this->categoryService->create($data);
        return ApiResponseHelper::success($category, 'Category created successfully', 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        $category = $this->categoryService->update($id, $data);
        return ApiResponseHelper::success($category, 'Category updated successfully');
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->delete($id);
            return ApiResponseHelper::success(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }
}
