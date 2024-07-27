<?php

namespace App\Observers;

use App\Models\Sale;
use App\Services\CommissionService;

class SaleObserver
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    public function created(Sale $sale)
    {
        $this->commissionService->distributeCommissions($sale);
    }
}