<div class="table-responsive">
    <table id="file-export" class="table table-bordered table-striped text-nowrap w-100">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($user->commissions as $item)
                <tr>
                    <td>
                        {{ $item->amount }} BV
                    </td>
                    <td>
                        {{ $item->created_at->format('jS,M Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-warning">no record available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
