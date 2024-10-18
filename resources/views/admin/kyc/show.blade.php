@extends('layouts.app')

@section('title', 'KYC Data')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/glightbox/css/glightbox.min.css') }}">
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">KYC Data</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card shadow-none border">
                <div class="card-body p-4">
                    <div class="text-center">
                        <span class="avatar avatar-xl avatar-rounded">
                            <img src="{{ $user->profile_picture }}" alt="">
                        </span>
                        <div class="mt-2">
                            <p class="mb-0 fw-semibold">{{ $user->full_name }}</p>
                            <p class="fs-12 op-7 mb-1 text-muted">{{ $user->email }}</p>
                            @if ($user->is_ambassador)
                                <span class="badge bg-info-transparent rounded-pill">Ambassador</span>
                            @else
                                <span class="badge bg-info-transparent rounded-pill">Customer</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="d-flex flex-wrap align-items-center">
                                <div class="me-2 fw-semibold">
                                    Phone :
                                </div>
                                <span class="fs-12 text-muted">{{ $user->phone_number }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xxl-8 col-xl-8 col-lg-8 col-md-8 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex mb-3 align-items-center justify-content-between">
                        <p class="mb-0 fw-semibold fs-14">Uploaded Documents</p>
                    </div>
                    <div class="table-responsive border border-bottom-0">
                        <table class="table text-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">File</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="files-list" id="table-body">
                                @forelse ($documents as $key=>$item)
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="avatar avatar-xs">
                                                        <img src="{{ $item->front_link }}" alt="">
                                                    </span>
                                                </div>
                                            </div>
                                        </th>
                                        <td>
                                            @if ($item->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif ($item->status === 'verified')
                                                <span class="badge bg-success"> Verified</span>
                                            @elseif ($item->status === 'declined')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->created_at->format('jS, M Y H:i A') }}</td>
                                        <td>
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ $item->front_link }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="View Document"
                                                    data-gallery="document{{ $key }}"
                                                    class="btn btn-icon btn-sm btn-info-transparent rounded-pill glightbox"><i
                                                        class="ri-eye-line "></i></a>
                                                @if ($item->status === 'pending')
                                                    <a href="javascript:void(0);" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Approve Document"
                                                        class="btn btn-icon btn-sm btn-success-transparent rounded-pill btn-action"
                                                        data-url="{{ route('admin.kyc.approve', $item->uuid) }}"
                                                        data-action="approve this document"><i
                                                            class="bx bx-user-check"></i></a>
                                                    <a href="javascript:void(0);" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Decline Document"
                                                        data-url="{{ route('admin.kyc.decline', $item->uuid) }}"
                                                        data-action="decline this document"
                                                        class="btn btn-icon btn-sm btn-danger-transparent rounded-pill btn-action"><i
                                                            class="bx bx-user-x"></i></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>
    <script>
        var lightboxVideo = GLightbox({
            selector: '.glightbox'
        });
        lightboxVideo.on('slide_changed', ({
            prev,
            current
        }) => {
            console.log('Prev slide', prev);
            console.log('Current slide', current);

            const {
                slideIndex,
                slideNode,
                slideConfig,
                player
            } = current;
        });
    </script>
    @include('admin.kyc.scritps._actions')
@endpush
