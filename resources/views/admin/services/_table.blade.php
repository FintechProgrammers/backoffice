@forelse ($services as $item)
    <tr>
        <td>
            {{ $item->name }} @if ($item->ambassadorship)
                <span class="badge bg-secondary">{{ __('Ambassadorship') }}</span>
            @endif
        </td>
        <td class="text-center">${{ number_format($item->price, 2) }}</td>
        <td class="text-center">{{ number_format($item->bv_amount, 2) }} BV</td>
        <td class="text-center">
            {{ convertDaysToUnit($item->duration, $item->duration_unit) . ' ' . $item->duration_unit }}
        </td>
        <td class="text-center">
            @if ($item->is_published)
                <span class="badge bg-success">{{ __('Published') }}</span>
            @else
                <span class="badge bg-warning">{{ __('Draft') }}</span>
            @endif
        </td>
        <td>
            <a aria-label="anchor" href="javascript:void(0);" class="" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-three-dots fs-22"></i>
            </a>
            <ul class="dropdown-menu" style="">
                {{-- <li class="mb-0">
                    <a href="{{ route('admin.package.show', $item->uuid) }}" class="dropdown-item">Details</a>
                </li> --}}
                @if ($loggedInUser->can('publish package'))
                    @if ($item->is_published)
                        <li class="mb-0">
                            <a href="javascript:void(0);" class="dropdown-item btn-action"
                                data-url="{{ route('admin.package.draft', $item->uuid) }}"
                                data-action="you want to set {{ $item->name }} as draft">Draft</a>
                        </li>
                    @else
                        <li class="mb-0">
                            <a href="javascript:void(0);" class="dropdown-item btn-action"
                                data-url="{{ route('admin.package.publish', $item->uuid) }}"
                                data-action="you want to publish {{ $item->name }}">Publish</a>
                        </li>
                    @endif
                @endif

                @if ($loggedInUser->can('edit package'))
                    <li class="mb-0">
                        <a href="{{ route('admin.package.edit', $item->uuid) }}" class="dropdown-item">Edit</a>
                    </li>
                @endif
                @if ($loggedInUser->can('delete package'))
                    <li class="mb-0">
                        <a href="javascript:void(0);" class="dropdown-item btn-action"
                            data-url="{{ route('admin.package.delete', $item->uuid) }}"
                            data-action="you want to delete {{ $item->name }}">Delete</a>
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
        {{ $services->links('vendor.pagination.custom') }}
    </td>
</tr>
