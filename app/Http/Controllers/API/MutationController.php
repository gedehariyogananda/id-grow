<?php

namespace App\Http\Controllers\API;

use App\Helper\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Mutation;
use App\Services\MutationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MutationController extends Controller
{
    protected $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    public function index()
    {
        try {
            $users = $this->mutationService->get(request(Mutation::$allowedParams));
            return ApiResponseHelper::paginated($users);
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $mutation = $this->mutationService->find($id);
            if (!$mutation) {
                return ApiResponseHelper::error('Mutation not found', 404);
            }
            return ApiResponseHelper::success($mutation, 'Mutation details retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|in:in,out',
                'description' => 'nullable|string',
                'mutation_code' => 'required|string|max:255|unique:mutations,mutation_code',
                'mutation_date' => 'required|date',
                'product_location_id' => 'required|exists:product_locations,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            $data['user_id'] = $request->user()->id;

            $mutation = $this->mutationService->create($data);
            return ApiResponseHelper::success($mutation, 'Mutation created successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|required|string|in:in,out',
                'description' => 'nullable|string',
                'mutation_code' => 'sometimes|required|string|max:255|unique:mutations,mutation_code,' . $id,
                'mutation_date' => 'sometimes|required|date',
                'product_location_id' => 'sometimes|required|exists:product_locations,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            $data['user_id'] = $request->user()->id;

            $mutation = $this->mutationService->update($id, $data);
            if (!$mutation) {
                return ApiResponseHelper::error('Mutation not found', 404);
            }
            return ApiResponseHelper::success($mutation, 'Mutation updated successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->mutationService->delete($id);
            if (!$deleted) {
                return ApiResponseHelper::error('Mutation not found', 404);
            }
            return ApiResponseHelper::success(null, 'Mutation deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }
}
