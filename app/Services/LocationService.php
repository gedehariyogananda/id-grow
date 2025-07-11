<?php

namespace App\Services;

use App\Repositories\LocationRepository;
use App\Repositories\UserRepository;

class LocationService extends BaseService
{
    protected $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        parent::__construct($locationRepository);
    }
}
