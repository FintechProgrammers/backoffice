<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered text-nowrap w-100">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Service</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th class="text-center">Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($user->subscriptions as $item)
                    <tr>
                        <td>
                            <a href="{{ route('admin.users.show', $item->user->uuid) }}">
                                <x-profile-component name="{{ $item->user->full_name }}" email="{{ $item->user->email }}"
                                    image="{{ $item->user->profile_picture }}" />
                            </a>
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
                            @if ($item->end_date->isPast())
                                <span class="badge bg-warning-transparent">{{ __('Expired') }}</span>
                            @else
                                <span class="badge bg-success-transparent">{{ __('Running') }}</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-primary btn-sm trigerModal"
                                data-url="{{ route('admin.users.membership.form', $item->uuid) }}"
                                data-bs-toggle="modal"
                                data-bs-target="#primaryModal">{{ __('Update Expiry Date') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center"><span class="text-warning">no data available</span></td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>
</div>
