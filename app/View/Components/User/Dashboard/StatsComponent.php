<?php

namespace App\View\Components\User\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatsComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $stats = [
            (object) [
                'title' => 'Purchases',
                'value' => 0,
                'color' => 'bg-primary',
                'icon' => 'bx bx-cart',
            ],
            (object) [
                'title' => 'Tickets',
                'value' => 0,
                'color' => 'bg-warning',
                'icon' => 'bx bx-support',
            ],
        ];

        $data['stats'] = $stats;

        return view('components.user.dashboard.stats-component',$data);
    }
}
