@forelse ($addresses as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->user->full_name }}" email="{{ $item->user->email }}"
                image="{{ $item->user->profile_picture }}" />
        </td>
        <td class="text-center">{{ $item->address }}</td>
        <td>{{ $item->created_at->format('jS, M Y H:i A') }}</td>
        <td>
            @if ($item->is_listed)
                <span class="badge bg-blue">Whitelisted</span>
            @else
                <a class="btn btn-primary btn-sm btn-action" href="#"
                    data-url="{{ route('admin.wallet.address.approve', $item->uuid) }}">
                    <div class="spinner-border" style="display: none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span id="text">Mark as Listed</span>
                </a>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="4" style="border: none;">
        {{ $addresses->links('vendor.pagination.custom') }}
    </td>
</tr>
