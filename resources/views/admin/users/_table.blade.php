@forelse ($users as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->full_name }}" email="{{ $item->email }}"
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
                <x-profile-component name="{{ $item->sponsor->full_name }}" email="{{ $item->sponsor->email }}"
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
                @if (Auth::guard('admin')->user()->can('set user as ambassador'))
                    @if (!$item->is_ambassador)
                        <li class="mb-0">
                            <a href="javascript:void(0);" class="dropdown-item trigerModal" data-bs-toggle="modal"
                                data-bs-target="#primaryModal"
                                data-url="{{ route('admin.users.ambassador.form', $item->uuid) }}"
                                data-action="Set user as Ambassador">Set as Ambassador</a>
                        </li>
                    @endif
                @endif

                @if (Auth::guard('admin')->user()->can('banned user'))
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
                @endif

                @if (Auth::guard('admin')->user()->can('delete user'))
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.users.delete', $item->uuid) }}" data-action="delete">Delete</a>
                    </li>
                @endif


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
