@extends('layouts.app')

@section('title', 'Settle Commission')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush

@section('content')
    <div class="row justify-content-center align-items-center">
        <div class="col-xxl-6 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12 ">
            <div class="text-center">
                <h5>Weekly Commission Settlement</h5>
                <p>Automatically settle and release commissions for eligible users based on weekly sales, ensuring accurate
                    payouts according to the defined settlement rules.</p>
            </div>
            <div class="card custom-card product-checkout">
                <div class="p-3">
                    <form action="{{ route('admin.commissions.settle.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                {{-- Sale Selection --}}
                                <div class="mb-3">
                                    <label for="sale">User</label>
                                    <select class="form-control users" name="user">
                                        <option value="" data-profile="" data-user="">
                                            Select</option>
                                        <option value="all">All</option>
                                        @foreach ($users as $item)
                                            <option value="{{ $item->id }}" data-profile="{{ $item->profile_picture }}"
                                                data-user="{{ $item->full_name }}">
                                                {{ $item->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sale">Week</label>
                                    <select class="form-control week" name="week">
                                        <option value="" data-profile="" data-user="">
                                            Select
                                        </option>
                                        @foreach ($weeks as $item)
                                            <option value="{{ $item['value'] }}">
                                                {{ $item['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('week')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="submit-btn-container" class="mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }

                // Retrieve profile URL from the data attribute
                var profileUrl = $(state.element).data('profile');

                // Use default image if profileUrl is undefined or empty
                if (!profileUrl || profileUrl === '') {
                    profileUrl = "{{ asset('assets/images/default-dp.png') }}";
                }

                // Create the formatted state with the profile image and text
                var $state = $(
                    '<span><img src="' + profileUrl +
                    '" class="img-circle" style="width: 30px; height: 30px; margin-right: 10px;" /> ' +
                    state.text + '</span>'
                );

                return $state;
            }

            // Initialize select2 with custom templates
            $('.users').select2({
                templateResult: formatState,
                templateSelection: formatState,
                placeholder: "Select a sale",
                allowClear: true
            });
        });
    </script>

    @if (Session::has('success'))
        <script>
            displayMessage('{{ Session::get('success') }}', "success");
        </script>
    @endif

    @if (Session::has('error'))
        <script>
            displayMessage('{{ Session::get('error') }}', "error");
        </script>
    @endif
@endpush
