@extends('layouts.app')

@section('title', 'Migrate User')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Migrate User</p>
        </div>
    </div>
    <div class="card custom-card product-checkout">
        <div class="p-3">
            <form action="{{ route('admin.users.import.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <h6>User Information</h6>
                        <div class="mb-3">
                            <label for="country">Sponsor</label>
                            <select class="form-control sponsors" data-trigger name="sponsor">
                                <option value="">Select</option>
                                @foreach ($users as $item)
                                    <option value="{{ $item->uuid }}" data-userid="{{ $item->uuid }}"
                                        data-profile="{{ $item->profile_picture }}">
                                        {{ $item->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sponsor')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="country">Country</label>
                            <select class="country-select form-control" name="country" data-trigger name="country">
                                <option value="">Select</option>
                                @foreach ($countries as $item)
                                    <option value="{{ $item->iso2 }}" data-countryName="{{ $item->name }}">
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="form-text1" class="form-label fs-14 text-dark">First name</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="ri-user-line"></i></div>
                                <input type="text" class="form-control" name="first_name" id="form-text1"
                                    placeholder="Enter first name">
                            </div>
                            @error('first_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="form-text1" class="form-label fs-14 text-dark">Last name</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="ri-user-line"></i></div>
                                <input type="text" class="form-control" name="last_name" id="form-text1"
                                    placeholder="Enter last name">
                            </div>
                            @error('last_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="form-text1" class="form-label fs-14 text-dark">Username</label>
                            <div class="input-group">
                                <div class="input-group-text">@</div>
                                <input type="text" class="form-control" name="username" id="form-text1"
                                    placeholder="Enter username">
                            </div>
                            @error('username')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="form-text1" class="form-label fs-14 text-dark">Email</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="ri-mail-line"></i></div>
                                <input type="text" class="form-control" id="form-text1" name="email"
                                    placeholder="Enter email">
                            </div>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h6 class="text-center">Choose a package to create your account.</h6>
                        <div class="mb-3">
                            <div class="row scrollable-container" style="max-height: 300px; overflow-y: auto;">
                                @forelse($packages as $item)
                                    <div class="col-lg-12 mb-2">
                                        <div class="form-check shipping-method-container mb-0 bxi-package"
                                            style="cursor: pointer;">
                                            <input class="service d-none" name="package" value="{{ $item->uuid }}"
                                                type="radio" class="form-check-input" data-name="{{ $item->name }}"
                                                data-image="{{ $item->image }}" data-price="{{ $item->price }}">
                                            <div class="form-check-label">
                                                <div class="d-sm-flex align-items-center justify-content-between">
                                                    <div class="d-flex">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-md">
                                                                <img src="{{ $item->image }}" alt="">
                                                            </span>
                                                        </div>
                                                        <div class="shipping-partner-details me-sm-5 me-0">
                                                            <p class="mb-0 fw-semibold">{{ $item->name }}</p>
                                                            <p class="text-muted fs-11 mb-0">
                                                                {{ convertDaysToUnit($item->duration, $item->duration_unit) . ' ' . $item->duration_unit }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="fw-semibold me-sm-5 me-0">
                                                        ${{ number_format($item->price, 2, '.', ',') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-lg-12">
                                        <x-no-datacomponent title="no package available" />
                                    </div>
                                @endforelse
                            </div>
                            @error('package')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <h6>Subscription Duration</h6>
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="">Start Date</label>
                                <input type="text" class="form-control date-picker" id="start_date" name="start_date"
                                    placeholder="Start Date">
                                @error('start_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">End Date</label>
                                <input type="text" class="form-control date-picker" id="end_date" name="end_date"
                                    placeholder="End Date">
                                @error('end_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">
                            <div class="spinner-border" style="display: none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <span id="text">Submit</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd", // Sets the date format
                changeMonth: true, // Allows changing months
                changeYear: true // Allows changing years
            });
        });

        $(document).ready(function() {

            function formatState(state) {
                if (!state.id) {
                    return state.text; // Return text for the placeholder option.
                }

                var profileUrl = $(state.element).data('profile'); // Get profile picture URL.
                var $state = $(
                    '<span><img src="' + profileUrl +
                    '" class="img-circle" style="width: 30px; height: 30px; margin-right: 10px;" /> ' + state
                    .text + '</span>'
                );
                return $state;
            }

            $('.sponsors').select2({
                templateResult: formatState,
                templateSelection: formatState,
                placeholder: "Select a sponsor", // Placeholder text.
                allowClear: true // Allow clearing the selection.
            });
        });
    </script>
    <script>
        document.querySelectorAll('.bxi-package').forEach(container => {
            container.addEventListener('click', function() {
                const radio = this.querySelector('input[name="package"]');

                if (radio) {
                    radio.checked = true;

                    // Remove highlight from all containers
                    document.querySelectorAll('.bxi-package').forEach(function(container) {
                        container.classList.remove('highlighted');
                    });

                    // Add highlight to the selected container
                    this.classList.add('highlighted');
                }
            });
        });
    </script>

    @if (Session::has('success'))
        <script>
            displayMessage('{{ Session::get('success') }}', "success")
        </script>
    @endif

    @if (Session::has('error'))
        <script>
            displayMessage('{{ Session::get('error') }}', "error")
        </script>
    @endif

@endpush
