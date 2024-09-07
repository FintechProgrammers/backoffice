@forelse ($referrals as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->full_name }}" email="{{ $item->email }}"
                image="{{ $item->profile_picture }}" />
        </td>
        <td class="text-center">{{ $item->username }}</td>
        <td class="text-center">
            @if ($item->is_ambassador)
                <span class="badge bg-blue">Ambassador</span>
            @else
                <span class="badge bg-black">Customer</span>
            @endif
        </td>
        <td class="text-center">${{ number_format($item->purchase->sum('amount'), 2, '.', ',') }}</td>
        <td>{{ $item->created_at->format('jS, M Y H:i A') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="5" style="border: none;">
        {{ $referrals->links('vendor.pagination.custom') }}
    </td>
</tr>
