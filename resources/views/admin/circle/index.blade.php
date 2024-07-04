@extends('layouts.app')

@section('title', 'Cycle')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Cycles</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <table class="table table-bordered text-nowrap w-100">
                        {{-- id="scroll-vertical" --}}
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @forelse ($cycles as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->start_date->format('jS,M Y') }}</td>
                                    <td>{{ $item->end_date->format('jS,M Y') }}</td>
                                    <td>
                                        @if ($item->is_active)
                                            <span class="badge bg-success-transparent">Running</span>
                                        @else
                                            <span class="badge bg-warning-transparent">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-warning">no cycle available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Datatables Cdn -->
@endpush
