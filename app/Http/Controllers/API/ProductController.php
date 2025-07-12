<?php

namespace App\Http\Controllers\API;

use App\Helper\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        try {
            $products = $this->productService->get(request(Product::$allowedParams));
            return ApiResponseHelper::paginated($products);
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function detail($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return ApiResponseHelper::error('Product not found', 404);
            }
            return ApiResponseHelper::success($product, 'Product details retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'name_product' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        $product = $this->productService->create($data);
        return ApiResponseHelper::success($product, 'Product created successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => 'sometimes|required|string|max:255|unique:products,product_code,' . $id,
            'name_product' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'unit_id' => 'sometimes|required|exists:units,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        $product = $this->productService->update($id, $data);
        if (!$product) {
            return ApiResponseHelper::error('Product not found', 404);
        }
        return ApiResponseHelper::success($product, 'Product updated successfully');
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->productService->delete($id);
            if (!$deleted) {
                return ApiResponseHelper::error('Product not found', 404);
            }
            return ApiResponseHelper::success(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }
}
