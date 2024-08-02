<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RankCard extends Component
{

    protected $rank;

    /**
     * Create a new component instance.
     */
    public function __construct($rank)
    {
        $this->rank = $rank;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $rank = \App\Models\Rank::where('id', $this->rank)->first();

        return view('components.rank-card', ['rank' => $rank]);
    }
}