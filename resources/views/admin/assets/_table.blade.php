@forelse ($assets as $item)
    <tr>
        <td>
            <div class="d-flex align-items-center lh-1">
                <div class="me-2">
                    <span class="avatar avatar-md avatar-rounded">
                        <img src="{{ $item->image }}" alt="">
                    </span>
                </div>
                <div>
                    <span class="d-block fw-semibold mb-0">{{ $item->name }}</span>
                </div>
            </div>
        </td>
        <td>
            {{ $item->symbol }}
        </td>
        <td>
            {{ $item->category->name }}
        </td>
        <td>
            <a aria-label="anchor" href="javascript:void(0);" class="" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-three-dots fs-22"></i>
            </a>
            <ul class="dropdown-menu" style="">
                @if ($loggedInUser->can('create asset'))
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item trigerModal" data-title="Update Asset"
                            data-url="{{ route('admin.assets.show', $item->uuid) }}" data-bs-toggle="modal"
                            data-bs-target="#primaryModal">Edit</a>
                    </li>
                @endif
                {{-- @if ($loggedInUser->can('delete category'))
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.product.delete', $item->uuid) }}"
                            data-action="you want to delete {{ $item->name }}">Delete</a>
                    </li>
                @endif --}}
            </ul>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="4" style="border: none;">
        {{ $assets->links('vendor.pagination.custom') }}
    </td>
</tr>
