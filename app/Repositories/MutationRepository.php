<?php

namespace App\Repositories;

use App\Models\Mutation;

class MutationRepository extends BaseRepository
{
    public function __construct(Mutation $mutation)
    {
        parent::__construct($mutation);
    }
}
