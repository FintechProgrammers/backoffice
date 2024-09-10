@forelse ($customers as $item)
    <tr>
        <td>
            <x-profile-component name="{{ $item->full_name }}" email="{{ $item->email }}"
                image="{{ $item->profile_picture }}" />
        </td>
        <td>
            @if (!empty($item->sponsor))
                <x-profile-component name="{{ $item->sponsor->full_name }}" email="{{ $item->sponsor->email }}"
                    image="{{ $item->sponsor->profile_picture }}" />
            @endif
        </td>
        {{-- <td>
            @if (!empty($item->subscriptions))
                <x-package-title title="{{ $item->subscriptions->service->name }}"
                    image="{{ $item->subscriptions->service->image }}"
                    price="{{ $item->subscriptions->service->price }}" />
            @else
                <span>No subscription</span>
            @endif
        </td> --}}
        <td>{{ $item->created_at->format('jS, M Y H:i A') }}</td>
        <td>
            <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"
                class="show-detail btn btn-primary btn-sm"
                data-url="{{ route('team.user.info', $item->uuid) }}">Details</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="4" style="border: none;">
        {{ $customers->links('vendor.pagination.custom') }}
    </td>
</tr>
