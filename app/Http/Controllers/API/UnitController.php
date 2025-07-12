<?php

namespace App\Http\Controllers\API;

use App\Helper\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        try {
            $products = $this->unitService->get(request(Unit::$allowedParams));
            return ApiResponseHelper::paginated($products);
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $unit = $this->unitService->find($id);
            if (!$unit) {
                return ApiResponseHelper::error('Unit not found', 404);
            }
            return ApiResponseHelper::success($unit, 'Unit details retrieved successfully');
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

        $unit = $this->unitService->create($data);
        return ApiResponseHelper::success($unit, 'Unit created successfully');
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

        $unit = $this->unitService->update($id, $data);
        if (!$unit) {
            return ApiResponseHelper::error('Unit not found', 404);
        }
        return ApiResponseHelper::success($unit, 'Unit updated successfully');
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->unitService->delete($id);
            if (!$deleted) {
                return ApiResponseHelper::error('Unit not found', 404);
            }
            return ApiResponseHelper::success(null, 'Unit deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }
}
