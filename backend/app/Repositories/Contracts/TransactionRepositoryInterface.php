<?php

namespace App\Repositories\Contracts;

use App\Repositories\BaseModelInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface extends BaseModelInterface
{
    public function forUser(int|string $userId): Collection;

    public function queryByUser(int|string $userId): Builder;
}
