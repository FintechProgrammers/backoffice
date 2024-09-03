<?php

namespace App\Services;

use App\Models\CommissionLevels;
use App\Models\CommissionTransaction;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;

class CommissionService
{

    public function distributeCommissions(Sale $sale)
    {
        try {
            $commissions = $this->calculateCommission($sale);

            foreach ($commissions as $commission) {
                CommissionTransaction::create([
                    'user_id' => $commission['parent_id'],
                    'sale_id' => $sale->id,
                    'level' => $commission['level'],
                    'amount' => $commission['amount'],
                    'child_id' => $commission['child_id'],
                    'cycle_id' => currentCycle()
                ]);
            }
        } catch (\Exception $e) {
            sendToLog($e);
        }
    }

    public function calculateCommission(Sale $sale)
    {
        $parent = User::find($sale->parent_id);
        $level = 0;
        $commissions = [];
        $childId = $sale->user_id;

        while ($parent && $level <= 4) {
            $commissionPlan = CommissionLevels::where('level', $level)->first();

            if (!$commissionPlan) {
                break;
            }

            // If commission plan has requirements and parent meets them, or if there are no requirements
            if (!$commissionPlan->has_requirement || $this->meetsRequirements($parent, $commissionPlan)) {
                if ($commissionPlan->bv_amount > 0) {
                    $commissions[] = [
                        'parent_id' => $parent->id,
                        'level' => $commissionPlan->level,
                        'amount' => ($sale->bv_amount * $commissionPlan->commission_percentage) / 100,
                        'child_id' => $childId
                    ];
                }
            }

            $childId = $parent->id;
            $parent = User::find($parent->parent_id);

            $level++;
        }

        return $commissions;
    }


    private function meetsRequirements(User $user, CommissionLevels $commissionPlan)
    {
        // Check if the user meets the requirements for the commission level
        $requirement = $commissionPlan->requirement()->first();

        if (!$requirement) {
            return true;
        }

        //check requirements
        $directBV = $this->calculateDirectBV($user);
        $sponsoredBV = $this->calculateSponsoredBV($user);
        $sponsoredCount = $this->calculateSponsoredCount($user);

        return $directBV >= $requirement->direct_bv &&
            $sponsoredBV >= $requirement->sponsored_bv &&
            $sponsoredCount >= $requirement->sponsored_count;
    }


    private function calculateDirectBV(User $user)
    {
        // Define the start and end of the month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $directSale = $user->directSales();

        $bv = $directSale->whereBetween('created_at', [$startOfMonth, $endOfMonth])->select('bv_amount')->sum('bv_amount');

        // get user direct bv
        return $bv;
    }

    private function calculateSponsoredBV(User $user)
    {
        return $user->team_volume;
    }

    private function calculateSponsoredCount(User $user)
    {
        $count = count($user->getDescendants());

        return $count;
    }
}
