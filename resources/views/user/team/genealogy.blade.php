@extends('layouts.user.app')

@section('title', 'Team')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Referral Genealogy</p>
        </div>
    </div>
    @if (count(directReferrals($user->id)) > 0)
        <div class="card card-body h-70">
            <div class="d-flex flex-column align-items-center">
                <div class="tree">
                    <ul id="content">
                        <li>
                            @if (count(directReferrals($user->id)) > 0)
                                @include('user.team.team-head', ['user' => $user])
                            @endif

                            @if (count(directReferrals($user->id)) > 0)
                                <ul>
                                    @foreach (directReferrals($user->id) as $child)
                                        <li>
                                            @include('user.team.team-head', ['user' => $child])
                                            @if (count(directReferrals($child->id)) > 0)
                                                <ul>
                                                    @foreach (directReferrals($child->id) as $grand_child)
                                                        <li>
                                                            @include('user.team.team-head', [
                                                                'user' => $grand_child,
                                                            ])
                                                            @if (count(directReferrals($grand_child->id)) > 0)
                                                                <ul>
                                                                    @foreach (directReferrals($grand_child->id) as $great_grand_child)
                                                                        <li>
                                                                            @include(
                                                                                'user.team.team-head',
                                                                                [
                                                                                    'user' => $great_grand_child,
                                                                                ]
                                                                            )
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif

                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    @else
        <div class="d-flex flex-column align-items-center h-100 ">
            <img src="{{ asset('assets/images/referral.png') }}" width="400px" height="400px" alt="">
            <h5 class="text-uppercase text-center"><b>Refer a friend</b></h5>
            <p class="text-center"> and earn commissions on each of their purchases!<br /> Share
                your referral link today and start earning rewards.</p>
            <div class="input-group mb-3 w-25">
                <input type="text" class="form-control" placeholder="Recipient's username"
                    value="{{ auth()->user()->referral_code }}" readonly aria-label="Recipient's username"
                    aria-describedby="button-addon2">
                <button class="btn btn-primary copy_btn"
                    copy_value="{{ route('register') }}?code={{ auth()->user()->referral_code }}" type="button"
                    id="button-addon2">Copy</button>
            </div>
        </div>
    @endif
    @include('user.team._user-details-modal')
@endsection
@push('scripts')
    @include('user.team.scripts._user-details')
@endpush
