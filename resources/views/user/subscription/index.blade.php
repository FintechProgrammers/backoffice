@extends('layouts.user.app')

@section('title', 'Subscriptions')

@section('content')
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                Subscriptions History
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-nowrap table-bordered">
                    {{-- <thead>
                        <tr>
                            <th scope="col">Service Name</th>
                            <th scope="col">Status</th>
                            <th scope="col" width="30%">Date</th>
                        </tr>
                    </thead> --}}
                    <tbody>
                        @forelse ($subscriptions as $item)
                            <tr>
                                <td>
                                    @if (!empty($item->service))
                                        <x-package-title title="{{ $item->service->name }}"
                                            image="{{ $item->service->image }}" price="{{ $item->service->price }}" />
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->end_date->isPast())
                                        <span class="badge bg-danger">Expired</span>
                                    @else
                                        <span class="badge bg-success">Running</span>
                                    @endif
                                </td>
                                <td>
                                    <b>Expiry Date:</b> {{ $item->end_date->format('jS,M Y') }}
                                </td>
                                <td>
                                    @if ($item->end_date->isPast())
                                        <a href="{{ route('package.details', $item->uuid) }}"
                                            class="btn btn-primary">Renew</a>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $higherPackages = $packages->filter(function ($package) use ($item) {
                                            return $package->price > $item->service->price;
                                        });
                                    @endphp

                                    @if ($higherPackages->isNotEmpty())
                                        <a href="{{ route('package.index') }}" class="btn btn-success">Upgrade</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center"><span class="text-warning">no data available</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
