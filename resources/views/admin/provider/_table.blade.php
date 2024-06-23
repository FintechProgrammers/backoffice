@forelse ($providers as $item)
    <tr>
        <td>
            {{ $item->name }}
        </td>
        <td class="text-center">
            @if ($item->can_payin)
                <span class="badge bg-blue">Yes</span>
            @else
                <span class="badge bg-danger">No</span>
            @endif
        </td>
        <td class="text-center">
            @if ($item->can_payout)
                <span class="badge bg-blue">Yes</span>
            @else
                <span class="badge bg-danger">No</span>
            @endif
        </td>
        <td class="text-center">
            @if ($item->is_crypto)
                <span class="badge bg-blue">Yes</span>
            @else
                <span class="badge bg-danger">No</span>
            @endif
        </td>
        <td class="text-center">
            @if ($item->is_active)
                <span class="badge bg-success">Enabled</span>
            @else
                <span class="badge bg-warning">Disabled</span>
            @endif
        </td>
        <td>
            <a aria-label="anchor" href="javascript:void(0);" class="" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-three-dots fs-22"></i>
            </a>
            <ul class="dropdown-menu" style="">
                <li class="mb-0">
                    <a href="#" class="dropdown-item trigerModal"
                        data-url="{{ route('admin.provider.edit', $item->uuid) }}" data-bs-toggle="modal"
                        data-bs-target="#primaryModal">Edit</a>
                </li>
                @if ($item->is_active)
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action text-danger"
                            data-url="{{ route('admin.provider.disable', $item->uuid) }}"
                            data-action="disable">Disable</a>
                    </li>
                @else
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.provider.enable', $item->uuid) }}" data-action="enable">Enable</a>
                    </li>
                @endif
            </ul>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
{{-- <tr style="border: none;">
    <td colspan="6" style="border: none;">
        {{ $providers->links('vendor.pagination.custom') }}
    </td>
</tr> --}}
