@extends('layouts.user.app')

@section('title', 'Bonus History')

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Wallet Earning History
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="file-export" class="table table-bordered table-striped text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bonuses as $item)
                                    <tr>
                                        <td>
                                            ${{ $item->amount }}
                                        </td>
                                        <td>
                                            {{ $item->created_at->format('jS,M Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-warning">no record available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
