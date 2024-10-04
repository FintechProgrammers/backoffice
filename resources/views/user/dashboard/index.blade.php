@extends('layouts.user.app')

@push('scripts')
@endpush

@section('title', 'Dashboard')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Welcome back, {{ ucfirst(auth()->user()->name) }}!</p>
            <span class="fs-semibold text-muted">Track your sales activity, leads and deals here.</span>
        </div>
    </div>
    @if (Auth::user()->is_ambassador)
        @include('user.dashboard._abassedor-dashboard')
    @else
        <div class="row">
            <div class="col-lg-12">
                @include('user.dashboard._benner')
            </div>
            <div class="col-lg-3">
                {{-- @include('user.dashboard._upgrade') --}}
                @include('user.dashboard._profile-card')
                {{-- <x-user-subscription /> --}}
            </div>
            <div class="col-lg-9">
                <x-user.dashboard.stats-component />
                <x-user-subscription />
                @include('user.dashboard._activities')
            </div>
        </div>
    @endif
    @include('profile.partials._profile-modal')
@endsection
@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/swiper.js') }}"></script>
    <!-- Apex Charts JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var recentActivity = document.getElementById('latest-timeline');
            if (recentActivity) {
                new SimpleBar(recentActivity, {
                    autoHide: true
                });
            }

            var myElement21 = document.getElementById('teams-nav');
            new SimpleBar(myElement21, {
                autoHide: true
            });

            var myElement3 = document.getElementById('global-nav');
            new SimpleBar(myElement21, {
                autoHide: true
            });

            var myElementEn = document.getElementById('teams-nav-enrol');
            new SimpleBar(myElementEn, {
                autoHide: true
            });
        });
    </script>
    @if (Auth::user()->profile_completion_percentage < 100)
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('profileUpdateModal'), {
                keyboard: false
            });
            myModal.show();
        </script>
    @endif

    <script>
        var smioptions = {
            series: [{{ isset($progress) ? $progress : 0 }}],
            chart: {
                type: 'radialBar',
                height: 100,
                offsetY: -20,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: "#fff",
                        strokeWidth: '97%',
                        margin: 5, // margin is in pixels
                        dropShadow: {
                            enabled: false,
                            top: 2,
                            left: 0,
                            color: '#999',
                            opacity: 1,
                            blur: 2
                        }
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: -2,
                            fontSize: '15px'
                        }
                    }
                }
            },
            colors: ["#845adf"],
            grid: {
                padding: {
                    top: 5
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.4,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 53, 91]
                },
            },
            labels: ['Average Results'],
        };
        var chart = new ApexCharts(document.querySelector("#circular-semi"), smioptions);
        chart.render();
    </script>


    <script>
        // Convert current time to EST (accounting for daylight saving time)
        function getESTDate() {
            let now = new Date();
            let utcOffset = now.getTimezoneOffset() * 60000; // Offset in milliseconds
            let estOffset = -5 * 60 * 60 * 1000; // EST is UTC-5
            let estDate = new Date(now.getTime() + utcOffset + estOffset);

            // Adjust for Daylight Saving Time (if applicable)
            let isDST = estDate.getTimezoneOffset() < now.getTimezoneOffset();
            if (isDST) {
                estDate = new Date(estDate.getTime() + (1 * 60 * 60 * 1000)); // Add one hour for DST
            }
            return estDate;
        }

        let currentDate = getESTDate();
        let currentWeekNumber = getWeekNumberForMonth(currentDate);
        let countdownDate = getMonthEndDate(currentDate);

        let daysElement = document.getElementById('days');
        let hoursElement = document.getElementById('hours');
        let minutesElement = document.getElementById('minutes');
        let secondsElement = document.getElementById('seconds');
        let weekNumberElement = document.getElementById('week-number');


        function getCurrentWeekOfMonth() {
            let today = getESTDate(); // Get current EST date
            let firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1); // First day of the current month

            // Calculate the difference in days between today and the first day of the month
            let dayOfMonth = today.getDate();

            // Calculate the current week number within the month
            let weekOfMonth = Math.ceil(dayOfMonth / 7);

            $('#week-number').html(weekOfMonth);
        }

        getCurrentWeekOfMonth();

        function getWeekNumberForMonth(date) {
            let firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
            let dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            let weekNumber = Math.ceil(((date.getDate() + (dayOfWeek === 0 ? 6 : dayOfWeek - 1)) / 7));
            return weekNumber;
        }

        function getMonthEndDate(date) {
            let weekNumber = getWeekNumberForMonth(date);
            let firstDayOfWeek = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);
            let lastDayOfWeek = new Date(firstDayOfWeek.getTime() + 6 * 86400000);
            return lastDayOfWeek;
        }

        function updateCountdown() {
            let now = getESTDate();
            let timeRemaining = countdownDate - now;

            if (timeRemaining <= 0) {
                // Reset the countdown for the next month
                currentDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
                currentWeekNumber = getWeekNumberForMonth(currentDate);
                countdownDate = getMonthEndDate(currentDate);
                timeRemaining = countdownDate - now;
            }

            let days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
            let hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

            daysElement.textContent = days.toString().padStart(2, '0');
            hoursElement.textContent = hours.toString().padStart(2, '0');
            minutesElement.textContent = minutes.toString().padStart(2, '0');
            secondsElement.textContent = seconds.toString().padStart(2, '0');
        }

        setInterval(updateCountdown, 1000); // Update the countdown every second
    </script>

    <script>
        fetch('{{ route('sales.data') }}') // Adjust the route to match your controller
            .then(response => response.json())
            .then(salesData => {
                // console.log(salesData);
                const labels = salesData.labels;
                const data = {
                    labels: labels,
                    datasets: [{
                        label: 'Sales Analytics',
                        backgroundColor: 'rgb(132, 90, 223)',
                        borderColor: 'rgb(132, 90, 223)',
                        data: salesData.total_amounts, // Use the data from your API
                    }]
                };

                const config = {
                    type: 'line',
                    data: data,
                    options: {}
                };

                const myChart = new Chart(
                    document.getElementById('chartjs-line'),
                    config
                );
            });
    </script>

@endpush
