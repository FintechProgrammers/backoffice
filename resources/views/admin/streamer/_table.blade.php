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
                    <a href="{{ route('admin.streamers.show', $item->uuid) }}" class="dropdown-item">Profile</a>
                </li>

                @if (Auth::guard('admin')->user()->can('create streamer'))
                    <li class="mb-0">
                        <a href="#" class="dropdown-item trigerModal"
                            data-url="{{ route('admin.streamers.edit', $item->uuid) }}" data-bs-toggle="modal"
                            data-bs-target="#primaryModal" data-title="Update Streamer">Edit</a>
                    </li>
                @endif

                @if (Auth::guard('admin')->user()->can('banned streamer'))
                    @if ($item->status === 'suspended')
                        <li class="mb-0">
                            <a href="javascript:void(0);" class="dropdown-item btn-action "
                                data-url="{{ route('admin.streamers.activate', $item->uuid) }}"
                                data-action="activate">Activate</a>
                        </li>
                    @else
                        <li class="mb-0">
                            <a href="javascript:void(0);" class="dropdown-item btn-action"
                                data-url="{{ route('admin.streamers.suspend', $item->uuid) }}"
                                data-action="suspend">Suspend</a>
                        </li>
                    @endif
                @endif

                @if (Auth::guard('admin')->user()->can('delete streamer'))
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.streamers.delete', $item->uuid) }}"
                            data-action="delete">Delete</a>
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
