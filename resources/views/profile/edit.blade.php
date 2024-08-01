@extends('layouts.user.app')

@section('title', 'Profile')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Profile</p>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-sm-flex d-block">
                    <ul class="nav nav-tabs nav-tabs-header mb-0 d-sm-flex d-block" role="tablist">
                        <li class="nav-item m-1">
                            <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                                href="#personal-info" aria-selected="true">Personal Information</a>
                        </li>
                        <li class="nav-item m-1">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#security"
                                aria-selected="true">Security</a>
                        </li>
                        <li class="nav-item m-1">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                href="#paymentMEthods" aria-selected="true">Payment Methods</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="personal-info" role="tabpanel">
                            @include('profile.partials._personal-profile')
                        </div>
                        <div class="tab-pane p-0" id="security" role="tabpanel">
                            @include('profile.partials._security')
                        </div>
                        <div class="tab-pane p-0" id="paymentMEthods" role="tabpanel">
                            @include('user.payment-methods._methods')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    @include('profile.scripts._update-profile-image')
    @include('profile.scripts._change-password')
    @include('profile.scripts._update-profile')
    <script>
        $('.make-default').click(function(e) {
            e.preventDefault();

            const button = $(this)

            const spinner = button.find('.spinner-border')
            const buttonTest = button.find('#text')

            $.ajax({
                url: $(this).data('url'),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    buttonTest.hide()
                    spinner.show()
                    button.attr('disabled', true)
                },
                success: function(response) {

                    // spinner.hide()
                    // buttonTest.show()

                    displayMessage(response.message, "success")

                    setTimeout(function() {
                        location.reload()
                    }, 2000); // 2000 milliseconds = 2 seconds

                },
                error: function(xhr, status, error) {
                    spinner.hide()
                    buttonTest.show()
                    button.attr('disabled', false)
                    // Handle error response

                    // Handle other error statuses
                    // console.log(xhr.responseJSON)
                    displayMessage(xhr.responseJSON.message, "error")

                }
            });

        })

        $('#add-card').click(function(e) {
            e.preventDefault();

            const button = $(this)

            const spinner = button.find('.spinner-border')
            const buttonTest = button.find('#text')

            $.ajax({
                url: $(this).data('url'),
                type: 'GET',
                beforeSend: function() {
                    buttonTest.hide()
                    spinner.show()
                    button.attr('disabled', true)
                },
                success: function(response) {

                    // spinner.hide()
                    // buttonTest.show()

                    setTimeout(function() {
                        location.href = response.data.route
                    }, 2000); // 2000 milliseconds = 2 seconds

                },
                error: function(xhr, status, error) {
                    spinner.hide()
                    buttonTest.show()
                    button.attr('disabled', false)
                    // Handle error response

                    // Handle other error statuses
                    // console.log(xhr.responseJSON)
                    displayMessage(xhr.responseJSON.message, "error")

                }
            });

        })
    </script>
@endpush
