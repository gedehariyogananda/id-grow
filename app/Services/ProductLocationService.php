<?php

namespace App\Services;

use App\Repositories\ProductLocationRepository;

class ProductLocationService extends BaseService
{
    protected $productLocationRepository;

    public function __construct(ProductLocationRepository $productLocationRepository)
    {
        parent::__construct($productLocationRepository);
    }
}
