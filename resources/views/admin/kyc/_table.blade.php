@forelse ($users as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->name }}" email="{{ $item->email }}"
                image="{{ $item->profile_picture }}" />
        </td>
        <td>{{ $item->username }}</td>
        <td class="text-center">
            @if ($item->is_ambassador)
                <span class="badge bg-blue">Ambassador</span>
            @else
                <span class="badge bg-black">Customer</span>
            @endif
        </td>
        <td>{{ $item->created_at->format('jS, M Y H:i A') }}</td>
        <td>
            @if ($item->status === 'active')
                <span class="badge bg-success">Active</span>
            @elseif ($item->status === 'suspended')
                <span class="badge bg-warning">Suspended</span>
            @endif
        </td>
        <td>
            @if ($item->kyc_is_verified)
                <span class="badge bg-success">Verified</span>
            @else
                <span class="badge bg-warning">Pending</span>
            @endif
        </td>
        <td>
            <a aria-label="anchor" href="javascript:void(0);" class="" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-three-dots fs-22"></i>
            </a>
            <ul class="dropdown-menu" style="">
                <li class="mb-0">
                    <a href="{{ route('admin.kyc.show', $item->uuid) }}" class="dropdown-item">Details</a>
                </li>
            </ul>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="6" style="border: none;">
        {{ $users->links('vendor.pagination.custom') }}
    </td>
</tr>
