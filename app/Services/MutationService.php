<?php

namespace App\Services;

use App\Repositories\MutationRepository;

class MutationService extends BaseService
{
    protected $mutationRepository;

    public function __construct(MutationRepository $mutationRepository)
    {
        parent::__construct($mutationRepository);
    }
}
