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
            if ($sale->ambassadorship) {
                return;
            }

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
        $commissions = [];
        $childId = $sale->user_id;

        $commissionLevels = CommissionLevels::all()->keyBy('level');
        $maxLevel = $commissionLevels->keys()->max();

        // Get the parent of the user who made the sale
        $parent = User::find($sale->parent_id);
        $level = 0;

        while ($parent && $level <= $maxLevel) {
            if (!isset($commissionLevels[$level])) {
                break; // No commission level defined for this level, stop the loop
            }

            $commissionLevel = $commissionLevels[$level];

            // Check if the parent meets the requirements for the current commission level
            if ($this->meetsRequirements($parent, $commissionLevel)) {
                $commissionAmount = ($sale->bv_amount * $commissionLevel->commission_percentage) / 100;
                $commissions[] = [
                    'parent_id' => $parent->id,
                    'level' => $level,
                    'amount' => $commissionAmount,
                    'child_id' => $childId
                ];
            }

            // Prepare for the next iteration
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
