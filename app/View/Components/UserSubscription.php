<?php

namespace App\View\Components;

use App\Models\Service;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserSubscription extends Component
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
        $data['subscriptions'] = auth()->user()->subscriptions;
        $data['packages'] = Service::where('is_published', true)->where('ambassadorship', false)->get();

        return view('components.user-subscription', $data);
    }
}