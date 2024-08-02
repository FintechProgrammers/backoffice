@forelse ($sales as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->user->full_name }}" email="{{ $item->user->email }}"
                image="{{ $item->user->profile_picture }}" />
        </td>
        <td class="text-center">
            @if ($item->user->is_ambassador)
                <span class="badge bg-blue">Ambassador</span>
            @else
                <span class="badge bg-black">Customer</span>
            @endif
        </td>
        <td>
            @if (!empty($item->user->sponsor))
                <x-profile-component name="{{ $item->user->sponsor->full_name }}"
                    email="{{ $item->user->sponsor->email }}" image="{{ $item->user->sponsor->profile_picture }}" />
            @endif
        </td>
        <td>
            <x-package-title title="{{ $item->service->name }}" image="{{ $item->service->image }}"
                price="{{ $item->service->price }}" />
        </td>
        <td>
            ${{ number_format($item->amount, 2, '.', ',') }}
        </td>
        <td>
            {{ number_format($item->bv_amount, 2, '.', ',') }} BV
        </td>
        <td>
            {{ $item->created_at->format('jS,M Y H:i A') }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center"><span class="text-warning">no data available</span>
        </td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="7" style="border: none;">
        {{ $sales->links('vendor.pagination.custom') }}
    </td>
</tr>
