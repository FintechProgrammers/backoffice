<?php

namespace App\Services;

use App\Models\CommissionLevels;
use App\Models\CommissionTransaction;
use App\Models\Sale;
use App\Models\User;

class CommissionService
{

    public function distributeCommissions(Sale $sale)
    {
        $commissions = $this->calculateCommission($sale);

        foreach ($commissions as $commission) {
            CommissionTransaction::create([
                'user_id' => $commission['user_id'],
                'sale_id' => $sale->id,
                'level' => $commission['level'],
                'amount' => $commission['amount'],
                'cycle_id' => currentCycle()
            ]);
        }
    }

    public function calculateCommission(Sale $sale, $level = 1,User $parent = null)
    {
        if ($level > 4) {
            return [];
        }

        $parent = $parent ?: User::find($sale->parent_id);
        if (!$parent) {
            return [];
        }

        $commissionPlan = CommissionLevels::where('level', $level)->first();
        if (!$commissionPlan) {
            return [];
        }

        $commissions = [];

        if (!$commissionPlan->has_requirement || $this->meetsRequirements($parent, $commissionPlan)) {
            $commissions[] = [
                'user_id' => $parent->id,
                'level' => $commissionPlan->level,
                'amount' => ($sale->amount * $commissionPlan->commission_percentage) / 100,
            ];
        }

        $parent = User::find($parent->parent_id);

        return array_merge($commissions, $this->calculateCommission($sale, $level + 1, $parent));
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
        // Calculate the user's direct BV (example logic)
        return 1000; // Placeholder
    }

    private function calculateSponsoredBV(User $user)
    {
        // Calculate the user's sponsored BV (example logic)
        return 1000; // Placeholder
    }

    private function calculateSponsoredCount(User $user)
    {
        // Calculate the user's sponsored count (example logic)
        return 2; // Placeholder
    }
}
