@forelse ($streamers as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->full_name }}" email="{{ $item->email }}"
                image="{{ $item->profile_picture }}" />
        </td>
        <td>
            {{ $item->email }}
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
            <a aria-label="anchor" href="javascript:void(0);" class="" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots fs-22"></i>
            </a>
            <ul class="dropdown-menu" style="">
                <li class="mb-0">
                    <a href="{{ route('admin.users.show', $item->uuid) }}" class="dropdown-item">Profile</a>
                </li>
                {{-- @if (Auth::guard('admin')->user()->can('set user as ambassador'))
                    @if (!$item->is_ambassador)
                        <li class="mb-0">
                            <a href="javascript:void(0);" class="dropdown-item trigerModal" data-bs-toggle="modal"
                                data-bs-target="#primaryModal"
                                data-url="{{ route('admin.users.ambassador.form', $item->uuid) }}"
                                data-action="Set user as Ambassador">Set as Ambassador</a>
                        </li>
                    @endif
                @endif --}}

                {{-- <li class="mb-0">
                    <a href="javascript:void(0);" class="dropdown-item trigerModal" data-bs-toggle="modal"
                        data-bs-target="#primaryModal" data-url="{{ route('admin.users.plan.form', $item->uuid) }}"
                        data-action="Set user as Ambassador">Activate Plan</a>
                </li> --}}

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
        <td colspan="5" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="5" style="border: none;">
        {{ $streamers->links('vendor.pagination.custom') }}
    </td>
</tr>