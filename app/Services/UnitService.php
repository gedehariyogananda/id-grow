<?php

namespace App\Services;

use App\Repositories\UnitRepository;

class UnitService extends BaseService
{
    protected $unitRepository;

    public function __construct(UnitRepository $unitRepository)
    {
        parent::__construct($unitRepository);
    }
}
