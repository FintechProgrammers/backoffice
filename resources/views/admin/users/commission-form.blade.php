@extends('layouts.app')

@section('title', 'Pay Commission')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush

@section('content')
    <div class="row justify-content-center align-items-center">
        <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12 ">
            <h5 class="text-center">Pay Commission</h5>
            <div class="card custom-card product-checkout">
                <div class="p-3">
                    <form action="{{ route('admin.commission.pay.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                {{-- Sale Selection --}}
                                <div class="mb-3">
                                    <label for="sale">Sale</label>
                                    <select class="form-control sales" name="sale">
                                        <option value="">Select</option>
                                        @foreach ($sales as $item)
                                            <option value="{{ $item->uuid }}"
                                                data-profile="{{ $item->service->image_url }}"
                                                data-user="{{ $item->user->full_name }}"
                                                data-parent="{{ $item->user->parent->username ?? 'N/A' }}"
                                                data-price="{{ number_format($item->amount, 2, '.', ',') }}"
                                                data-bv="{{ number_format($item->bv_amount, 2, '.', ',') }}"
                                                data-date="{{ $item->created_at->format('jS,M Y H:i A') }}">
                                                {{ optional($item->service)->name }} - {{ $item->user->username }}
                                                (${{ number_format($item->amount, 2, '.', ',') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sale')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Sale Information Display --}}
                                <div id="sale-info" class="mt-3" style="display: none;">
                                    <h6>Sale Details</h6>
                                    <p><strong>User:</strong> <span id="user-name"></span></p>
                                    <p><strong>Parent:</strong><span id="parent-name"></span></p>
                                    <p><strong>Price:</strong> $<span id="price"></span></p>
                                    <p><strong>BV Amount:</strong> <span id="bv-amount"></span>BV</p>
                                    <p><strong>Date:</strong> <span id="sale-date"></span></p>
                                </div>

                                <div id="submit-btn-container" class="mt-3" style="display: none;">
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

                var profileUrl = $(state.element).data('profile');
                var $state = $(
                    '<span><img src="' + profileUrl +
                    '" class="img-circle" style="width: 30px; height: 30px; margin-right: 10px;" /> ' + state
                    .text + '</span>'
                );
                return $state;
            }

            $('.sales').select2({
                templateResult: formatState,
                templateSelection: formatState,
                placeholder: "Select a sale",
                allowClear: true
            });

            // Event listener for sale selection
            $('.sales').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                if (selectedOption.val() !== '') {
                    $('#user-name').text(selectedOption.data('user'));
                    $('#parent-name').text(selectedOption.data('parent'));
                    $('#price').text(selectedOption.data('price'));
                    $('#bv-amount').text(selectedOption.data('bv'));
                    $('#sale-date').text(selectedOption.data('date'));
                    $('#sale-info').show(); // Show the sale information
                    $('#submit-btn-container').show(); // Show the submit button
                } else {
                    $('#sale-info').hide(); // Hide if no sale is selected
                    $('#submit-btn-container').hide(); // Hide the submit button
                }
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
