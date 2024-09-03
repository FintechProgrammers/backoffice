<div class="table-responsive">
    <table id="file-export" class="table table-bordered table-striped text-nowrap w-100">
        <thead>
            <tr>
                <td>Associate</td>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($user->commissionTransactions as $item)
                <tr>
                    <td>
                        @if ($item->associate)
                            <a href="{{ route('admin.users.show', $item->associate->uuid) }}">
                                <x-profile-component name="{{ $item->associate->full_name }}"
                                    email="{{ $item->associate->email }}"
                                    image="{{ $item->associate->profile_picture }}" />
                            </a>
                        @endif
                    </td>
                    <td>
                        ${{ number_format($item->amount, 2, '.', ',') }}
                    </td>
                    <td>
                        {{ $item->created_at->format('jS,M Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-warning">no record available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
