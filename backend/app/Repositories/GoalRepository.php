<?php

namespace App\Repositories;

use App\Models\Goal;
use App\Repositories\Contracts\GoalRepositoryInterface;

class GoalRepository extends BaseModel implements GoalRepositoryInterface
{
    public function __construct(Goal $model)
    {
        parent::__construct($model);
    }
}
