<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TransactionRepository extends BaseModel implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function forUser(int|string $userId): Collection
    {
        return $this->query()->where('user_id', $userId)->with('category')->get();
    }

    public function queryByUser(int|string $userId): Builder
    {
        return $this->query()->where('user_id', $userId);
    }
}
