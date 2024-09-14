@forelse ($commissions as $item)
    <tr>
        <td>
            @if (!empty($item->sale))
                <x-package-title title="{{ $item->sale->service->name }}" image="{{ $item->sale->service->image }}"
                    price="{{ $item->sale->amount }}" />
            @endif
        </td>
        <td>
            @if (!empty($item->user))
                <x-profile-component name="{{ $item->user->full_name }}" email="{{ $item->user->email }}"
                    image="{{ $item->user->profile_picture }}" />
            @endif
        </td>
        <td>
            @if (!empty($item->associate))
                <x-profile-component name="{{ $item->associate->full_name }}" email="{{ $item->associate->email }}"
                    image="{{ $item->associate->profile_picture }}" />
            @endif
            @if ($item->level == 0)
                <span class="badge bg-info">Direct</span>
            @else
                <span class="badge bg-info">Indirect</span>
            @endif
        </td>
        <td>
            ${{ number_format($item->amount, 2) }}
        </td>
        <td>
            {{ $item->created_at->format('jS,M Y') }}
        </td>
        <td>
            @if ($item->is_converted)
                <span class="badge bg-success">Settled</span>
            @else
                <span class="badge bg-warning">Pending</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-warning">no record available</td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="6" style="border: none;">
        {{ $commissions->links('vendor.pagination.custom') }}
    </td>
</tr>
