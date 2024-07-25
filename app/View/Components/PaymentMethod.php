<?php

namespace App\View\Components;

use App\Models\Provider;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PaymentMethod extends Component
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
        $data['providers'] = Provider::where('is_active', true)->where('can_payin', true)->get();

        return view('components.payment-method', $data);
    }
}
