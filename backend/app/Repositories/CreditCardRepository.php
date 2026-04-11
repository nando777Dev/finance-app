<?php

namespace App\Repositories;

use App\Models\CreditCard;
use App\Repositories\Contracts\CreditCardRepositoryInterface;

class CreditCardRepository extends BaseModel implements CreditCardRepositoryInterface
{
    public function __construct(CreditCard $model)
    {
        parent::__construct($model);
    }
}
