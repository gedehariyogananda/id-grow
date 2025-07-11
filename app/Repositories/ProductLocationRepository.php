<?php

namespace App\Repositories;

use App\Models\ProductLocation;

class ProductLocationRepository extends BaseRepository
{
    public function __construct(ProductLocation $productLocation)
    {
        parent::__construct($productLocation);
    }
}
