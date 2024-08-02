@forelse ($subscriptions as $item)
    <tr>
        <td>
            <a href="{{ route('admin.users.show', $item->user->uuid) }}">
                <x-profile-component name="{{ $item->user->full_name }}" email="{{ $item->user->email }}"
                    image="{{ $item->user->profile_picture }}" />
            </a>
        </td>
        <td>
            @if (!empty($item->user->sponsor))
                <x-profile-component name="{{ $item->user->sponsor->full_name }}"
                    email="{{ $item->user->sponsor->email }}" image="{{ $item->user->sponsor->profile_picture }}" />
            @endif
        </td>
        <td>
            @if (!empty($item->service))
                <x-package-title title="{{ $item->service->name }}" image="{{ $item->service->image }}"
                    price="{{ $item->service->price }}" />
            @endif
        </td>
        <td>
            {{ $item->start_date->format('jS,M Y H:i A') }}
        </td>
        <td>
            {{ $item->end_date->format('jS,M Y H:i A') }}
        </td>
        <td class="text-center">
            @if ($item->is_active)
                <span class="badge bg-success-transparent">Active</span>
            @else
                <span class="badge bg-warning-transparent">Expired</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center"><span class="text-warning">no data available</span></td>
    </tr>
@endforelse
<tr style="border: none;">
    <td colspan="5" style="border: none;">
        {{ $subscriptions->links('vendor.pagination.custom') }}
    </td>
</tr>
