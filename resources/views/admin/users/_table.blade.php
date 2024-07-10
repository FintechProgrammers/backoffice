@forelse ($users as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->name }}" email="{{ $item->email }}"
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
        <td>
            @if (!empty($item->sponsor))
                <x-profile-component name="{{ $item->sponsor->name }}" email="{{ $item->sponsor->email }}"
                    image="{{ $item->sponsor->profile_picture }}" />
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
                    <a href="{{ route('admin.users.show', $item->uuid) }}" class="dropdown-item">Profile</a>
                </li>
                @if (!$item->is_ambassador)
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.users.mark.ambassador', $item->uuid) }}"
                            data-action="Set user as Ambassador">Set as Ambassador</a>
                    </li>
                @endif
                @if ($item->status === 'suspended')
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.users.activate', $item->uuid) }}"
                            data-action="activate">Activate</a>
                    </li>
                @else
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.users.suspend', $item->uuid) }}"
                            data-action="suspend">Suspend</a>
                    </li>
                @endif
                <li class="mb-0">
                    <a href="javascript:void(0);" class="dropdown-item btn-action"
                        data-url="{{ route('admin.users.delete', $item->uuid) }}" data-action="delete">Delete</a>
                </li>
            </ul>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="7" style="border: none;">
        {{ $users->links('vendor.pagination.custom') }}
    </td>
</tr>
