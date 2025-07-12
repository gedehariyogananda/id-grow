<?php

namespace App\Http\Controllers\API;

use App\Helper\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index()
    {
        try {
            $users = $this->locationService->get(request(Location::$allowedParams));
            return ApiResponseHelper::paginated($users);
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $location = $this->locationService->find($id);
            if (!$location) {
                return ApiResponseHelper::error('Location not found', 404);
            }
            return ApiResponseHelper::success($location, 'Location details retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_code' => 'required|string|max:255|unique:locations,location_code',
            'location_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        $location = $this->locationService->create($data);
        return ApiResponseHelper::success($location, 'Location created successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location_code' => 'required|string|max:255|unique:locations,location_code,' . $id,
            'location_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        $location = $this->locationService->update($id, $data);
        if (!$location) {
            return ApiResponseHelper::error('Location not found', 404);
        }
        return ApiResponseHelper::success($location, 'Location updated successfully');
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->locationService->delete($id);
            if (!$deleted) {
                return ApiResponseHelper::error('Location not found', 404);
            }
            return ApiResponseHelper::success(null, 'Location deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), 500);
        }
    }
}
