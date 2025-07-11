<?php

namespace App\Http\Controllers\API;

use App\Helper\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ProductLocation;
use App\Services\ProductLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductLocationController extends Controller
{
    protected $productLocationService;

    public function __construct(ProductLocationService $productLocationService)
    {
        $this->productLocationService = $productLocationService;
    }

    public function index()
    {
        try {
            $products = $this->productLocationService->get(request(ProductLocation::$allowedParams));
            return ApiResponseHelper::paginated($products);
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $productLocation = $this->productLocationService->find($id);
            if (!$productLocation) {
                return ApiResponseHelper::error('Product Location not found', 404);
            }
            return ApiResponseHelper::success($productLocation, 'Product Location details retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'location_id' => 'required|exists:locations,id',
                'stock' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();

            $productLocation = $this->productLocationService->create($data);
            return ApiResponseHelper::success($productLocation, 'Product Location created successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'sometimes|required|exists:products,id',
                'location_id' => 'sometimes|required|exists:locations,id',
                'stock' => 'sometimes|required|integer|min:0',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();

            $productLocation = $this->productLocationService->update($id, $data);
            if (!$productLocation) {
                return ApiResponseHelper::error('Product Location not found', 404);
            }
            return ApiResponseHelper::success($productLocation, 'Product Location updated successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->productLocationService->delete($id);
            if (!$deleted) {
                return ApiResponseHelper::error('Product Location not found', 404);
            }
            return ApiResponseHelper::success(null, 'Product Location deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }
}
