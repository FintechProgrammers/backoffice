@forelse ($sales as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->user->full_name }}" email="{{ $item->user->username }}"
                image="{{ $item->user->profile_picture }}" />
        </td>
        <td>
            @if (!empty($item->user->sponsor))
                <x-profile-component name="{{ $item->user->sponsor->full_name }}"
                    email="{{ $item->user->sponsor->username }}" image="{{ $item->user->sponsor->profile_picture }}" />
            @else
                -----
            @endif
        </td>
        <td>
            <x-package-title title="{{ $item->service->name }}" image="{{ $item->service->image }}"
                price="{{ $item->service->price }}" />
        </td>
        <td class="text-center">${{ number_format($item->amount, 2) }}</td>
        <td class="text-center">
            {{ $item->created_at->format('jS, M Y H:i A') }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="5" style="border: none;">
        {{ $sales->links('vendor.pagination.custom') }}
    </td>
</tr>
