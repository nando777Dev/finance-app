<?php

namespace App\Repositories;

use App\Models\BankAccount;
use App\Repositories\Contracts\BankAccountRepositoryInterface;

class BankAccountRepository extends BaseModel implements BankAccountRepositoryInterface
{
    public function __construct(BankAccount $model)
    {
        parent::__construct($model);
    }
}
