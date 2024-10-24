@extends('layouts.app')

@push('styles')
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Support Subjects</p>
        </div>
        <div class="btn-list mt-md-0 mt-2">
            @if ($loggedInUser->can('create support subject'))
                <button type="button" class="btn btn-primary btn-wave trigerModal"
                    data-url="{{ route('admin.support.subjects.create') }}" data-bs-toggle="modal"
                    data-bs-target="#primaryModal">
                    <i class="las la-user-plus me-2 align-middle d-inline-block"></i>{{ __('Create Subject') }}
                </button>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100">
                            {{-- id="scroll-vertical" --}}
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th width="10">Action</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('admin.support.subject.scripts._load-table')
    @include('admin.support.subject.scripts._submit-form')
    @include('admin.support.subject.scripts._actions')
@endpush
