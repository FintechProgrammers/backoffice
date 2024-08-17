@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Profile</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">User Profile</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-4 col-xl-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-sm-flex align-items-top p-4 border-bottom-0 main-profile-cover">
                        <div>
                            <span class="avatar avatar-xxl avatar-rounded online me-3">
                                <img src="{{ $user->profile_picture }}" alt="">
                            </span>
                        </div>
                        <div class="flex-fill main-profile-info">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-1 text-fixed-white">{{ Str::upper($user->name) }}</h6>
                            </div>
                            <p class="mb-1 text-muted text-fixed-white op-7">
                                @if ($user->is_ambassador)
                                    <span class="badge bg-blue">Ambassador</span>
                                @else
                                    <span class="badge bg-black">Customer</span>
                                @endif
                            </p>
                            <p class="fs-12 text-fixed-white mb-4 op-5">
                                <span class="me-3"><i class="fe fe-mail me-1 align-middle"></i>{{ $user->email }}</span>
                                <span class="me-3"><i
                                        class="fe fe-user me-1 align-middle"></i>{{ $user->username }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="p-4 border-bottom border-block-end-dashed">
                        <div class="d-flex justify-content-center gap-3">
                            @if (Auth::guard('admin')->user()->can('edit user'))
                                <a href="#" class="btn btn-sm btn-dark trigerModal"
                                    data-url="{{ route('admin.users.change-username', $user->uuid) }}"
                                    data-bs-toggle="modal" data-bs-target="#primaryModal">Change Username</a>
                            @endif

                            @if (!$user->is_ambassador && Auth::guard('admin')->user()->can('set user as ambassador'))
                                <a href="#" class="btn btn-sm btn-dark btn-action"
                                    data-url="{{ route('admin.users.mark.ambassador', $user->uuid) }}"
                                    data-action="Set user as Ambassador">Set as Ambassador</a>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 border-bottom border-block-end-dashed">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Contact Information :</p>
                        <div class="text-muted">
                            <p class="mb-2">
                                <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                    <i class="ri-mail-line align-middle fs-14"></i>
                                </span>
                                {{ $user->email }}
                            </p>
                            @if (!empty($user->phone_number))
                                <p class="mb-2">
                                    <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                        <i class="ri-phone-line align-middle fs-14"></i>
                                    </span>
                                    {{ $user->phone_number }}
                                </p>
                            @endif
                            <p class="mb-0">
                                <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                    <i class="ri-map-pin-line align-middle fs-14"></i>
                                </span>
                                {{ $user->userProfile->address }}
                            </p>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Subscription :</p>
                        @if (!empty($user->subscriptions))
                            <ul class="list-group">
                                @forelse ($user->subscriptions as $item)
                                    <li class="list-group-item">
                                        <div class="d-sm-flex">
                                            <span class="avatar avatar-sm">
                                                <img src="{{ $item->service->image }}" alt="img">
                                            </span>
                                            <div class="ms-sm-2 ms-0 mt-sm-0 mt-1 fw-semibold flex-fill">
                                                <p class="mb-0 lh-1">{{ $item->service->name }}</p>
                                                <span class="fs-11 text-muted op-7">
                                                    @if ($item->service->serviceProduct->isNotEmpty())
                                                        {{ $item->service->serviceProduct->pluck('product.name')->implode(', ') }}
                                                    @else
                                                        No products available.
                                                    @endif
                                                </span>
                                            </div>
                                            @if ($item->end_date->isPast())
                                                <span class="bg-warning">{{ __('Expired') }}</span>
                                            @else
                                                <span class="text-success">{{ __('Running') }}</span>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                @endforelse
                            </ul>
                        @else
                            <div class="d-flex justify-content-center">
                                <h6 class="text-warning">no subscriptions</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-8 col-xl-12">
            <div class="card custom-card">
                <div class="card-body p-0">
                    <div
                        class="p-3 border-bottom border-block-end-dashed d-flex align-items-center justify-content-between">
                        <div>
                            <ul class="nav nav-tabs mb-0 tab-style-6 justify-content-start" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="activity-tab" data-bs-toggle="tab"
                                        data-bs-target="#activity-tab-pane" type="button" role="tab"
                                        aria-controls="activity-tab-pane" aria-selected="true"><i
                                            class="ri-gift-line me-1 align-middle d-inline-block"></i>Activity</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile" data-bs-toggle="tab"
                                        data-bs-target="#profile-pan" type="button" role="tab"
                                        aria-controls="profile-pan" aria-selected="false"><i
                                            class="ri-bill-line me-1 align-middle d-inline-block"></i>Profile</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="withdrawals" data-bs-toggle="tab"
                                        data-bs-target="#withdrawals-pan" type="button" role="tab"
                                        aria-controls="withdrawals-pan" aria-selected="false"><i
                                            class="ri-money-dollar-box-line me-1 align-middle d-inline-block"></i>Withdrawals</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="bonuses" data-bs-toggle="tab"
                                        data-bs-target="#bonuses-pan" type="button" role="tab"
                                        aria-controls="bonuses-pan" aria-selected="false"><i
                                            class="ri-exchange-box-line me-1 align-middle d-inline-block"></i>Commisions</button>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <p class="fw-semibold mb-2">Profile {{ $user->profile_completion_percentage }}% completed</p>
                            <div class="progress progress-xs progress-animate">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    aria-valuenow="{{ $user->profile_completion_percentage }}" aria-valuemin="0"
                                    aria-valuemax="100" style="width: {{ $user->profile_completion_percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane show active fade p-0 border-0" id="activity-tab-pane" role="tabpanel"
                                aria-labelledby="activity-tab" tabindex="0">
                                @include('admin.users._activities')
                            </div>
                            <div class="tab-pane fade p-0 border-0" id="profile-pan" role="tabpanel"
                                aria-labelledby="posts-tab" tabindex="0">
                                @include('admin.users._profile')
                            </div>
                            <div class="tab-pane fade p-0 border-0" id="withdrawals-pan" role="tabpanel"
                                aria-labelledby="followers-tab" tabindex="0">
                                @include('admin.users._withdrawals')
                            </div>
                            <div class="tab-pane fade p-0 border-0" id="bonuses-pan" role="tabpanel"
                                aria-labelledby="gallery-tab" tabindex="0">
                                @include('admin.users._bonus_history')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('admin.users.scritps._update-form')
    @include('profile.scripts._update-profile')
    <script>
        $('.btn-action').click(function(e) {
            e.preventDefault();

            var actionUrl = $(this).data('url');
            var actionType = $(this).data('action');

            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to ' + actionType + ' user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: actionType
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request based on action type
                    $.ajax({
                        url: actionUrl,
                        type: 'POST', // Assuming you're using GET method
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            // Assuming success message is returned from the server

                            displayMessage(response.message, "success")
                            // Assuming you want to reload the table after action is performed
                            // You can customize this part based on your requirement
                            // window.location.reload();
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            displayMessage(
                                'An error occurred while performing the action.',
                                "error")
                            // Handle error response
                            // You can display error messages or handle the error based on your requirement
                        }
                    });
                }
            });
        })
    </script>
@endpush
