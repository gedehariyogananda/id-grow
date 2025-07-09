<?php

namespace App\Repositories;

use App\Base\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(protected User $user)
    {
        parent::__construct($user);
    }
}
