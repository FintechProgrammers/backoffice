@forelse ($withdrawals as $item)
    <tr>
        <td>
            <span class="text-success fw-semibold">{{ $item->internal_reference }}</span>
        </td>
        {{-- <td>
            @if (!empty($item->associatedUser))
                <x-profile-component name="{{ $item->associatedUser->full_name }}"
                    email="{{ $item->associatedUser->email }}" image="{{ $item->associatedUser->profile_picture }}" />
            @endif
        </td> --}}
        <td> ${{ number_format($item->amount, 2, '.', ',') }}</td>
        <td>
            ${{ number_format($item->closing_balance, 2, '.', ',') }}
        </td>
        <td>
            @if ($item->action == 'credit')
                <span class="badge bg-success">Credit</span>
            @else
                <span class="badge bg-danger">Debit</span>
            @endif
        </td>
        <td>
            <span class="badge bg-secondary text-capitalize">{{ $item->type }}</span>
        </td>
        <td>
            @if ($item->status === 'completed')
                <span class="badge bg-success">Completed</span>
            @elseif ($item->status === 'declined')
                <span class="badge bg-danger">Declined</span>
            @else
                <span class="badge bg-warning">Pending</span>
            @endif
        </td>
        <td>
            {{ $item->created_at->format('jS,M Y H:i A') }}
        </td>
        <td></td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center">
            <span class="text-warning">no data available</span>
        </td>
    </tr>
@endforelse
