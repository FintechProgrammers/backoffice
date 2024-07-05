<table class="table text-nowrap table-bordered">
    <thead>
        <tr>
            <th scope="col">Reference</th>
            <th scope="col">Amount</th>
            <th scope="col">status</th>
            <th scope="col" width="30%">Date</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @forelse ($user->withdrawals as $item)
            <tr>
                <td>
                    <span class="text-success fw-semibold">{{ $item->reference }}</span>
                </td>
                <td>
                    ${{ $item->amount }}
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
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">
                    <span class="text-warning">no data available</span>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
