<?php

namespace App\Repositories;

use App\Models\Location;

class LocationRepository extends BaseRepository
{
    public function __construct(Location $location)
    {
        parent::__construct($location);
    }
}
