@forelse ($commissions as $item)
    <tr>
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
        <td colspan="4" class="text-center text-warning">no record available</td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="4" style="border: none;">
        {{ $commissions->links('vendor.pagination.custom') }}
    </td>
</tr>
