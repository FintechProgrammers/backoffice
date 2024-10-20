<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CryptoAddress;
use App\Notifications\AddressWhitelisted;
use Illuminate\Http\Request;

class WalletAddressController extends Controller
{
    function index()
    {
        return view('admin.walletAddress.index');
    }

    function filter(Request $request)
    {
        $search = $request->filled('search') ? $request->search : null;

        $address = CryptoAddress::query();

        $address = $address->when(!empty($search), fn($query) => $query->where('address', 'LIKE', "%{$search}%"))
            ->orderByRaw('IS_LISTED ASC'); // Assuming 'is_listed' is a boolean

        $data['addresses'] = $address->paginate(20);

        return view('admin.walletAddress._table', $data);
    }

    function approve(CryptoAddress $address)
    {
        $address->update([
            'is_listed' => true,
        ]);

        $user = $address->user;

        // Send notification to the user
        $user->notify(new AddressWhitelisted());

        return $this->sendResponse([], "Address Marked as Listed successfully.");
    }
}
