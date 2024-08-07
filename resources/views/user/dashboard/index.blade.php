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
                @include('user.dashboard._upgrade')
                @include('user.dashboard._profile-card')
            </div>
            <div class="col-lg-9">
                <x-user.dashboard.stats-component />
                @if (!empty(auth()->user()->subscriptions))
                    @include('user.dashboard._purchases')
                @endif
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
    <script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
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
        });
    </script>
    @if (Auth::user()->profile_completion_percentage < 100)
        {{-- <script>
            var myModal = new bootstrap.Modal(document.getElementById('profileUpdateModal'), {
                keyboard: false
            });
            myModal.show();
        </script> --}}
    @endif
    <script>
        Chart.defaults.borderColor = "rgba(142, 156, 173,0.1)", Chart.defaults.color = "#8c9097";
        const labels = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
        ];
        const data = {
            labels: labels,
            datasets: [{
                label: 'Sales Analytics',
                backgroundColor: 'rgb(132, 90, 223)',
                borderColor: 'rgb(132, 90, 223)',
                data: [0, 10, 5, 2, 20, 30, 45],
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
    </script>
    {{-- @include('partials.scripts.initiate-payin') --}}
    <script>
        $.ajax({
            url: "{{ route('week.clock') }}",
            type: 'GET',
            success: function(response) {
                const weekStart = new Date(response.data.week_start);
                const weekEnd = new Date(response.data.week_end);
                startTimer(weekStart, weekEnd);
            },
            error: function(xhr, status, error) {
                console.log(error)
            }
        });

        function startTimer(weekStart, weekEnd) {
            const now = new Date();
            const totalTime = weekEnd.getTime() - weekStart.getTime();
            const elapsedTime = now.getTime() - weekStart.getTime();

            // Calculate remaining time
            const remainingTime = Math.max(weekEnd.getTime() - now.getTime(), 0); // Ensure non-negative value

            const days = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
            const hours = Math.floor((remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

            // Update clock display
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');

            // Update progress bar (percentage)
            const progress = Math.min(elapsedTime / totalTime, 1) * 100;
            document.getElementById('progress-bar').style.width = `${progress}%`;
        }
    </script>
    <script>
        var smioptions = {
            series: [76],
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
        var markers = [{
                name: 'Russia',
                coords: [61, 105],
                style: {
                    fill: '#28d193'
                }
            },
            {
                name: 'Geenland',
                coords: [72, -42],
                style: {
                    fill: '#ff8c33'
                }
            },
            {
                name: 'Canada',
                coords: [56, -106],
                style: {
                    fill: '#ff534d'
                }
            },
            {
                name: 'Palestine',
                coords: [31.5, 34.8],
                style: {
                    fill: '#ffbe14'
                }
            },
            {
                name: 'Brazil',
                coords: [-14.2350, -51.9253],
                style: {
                    fill: '#4b9bfa'
                }
            },
        ];
        var map = new jsVectorMap({
            map: 'world_merc',
            selector: '#users-map',
            markersSelectable: true,

            onMarkerSelected(index, isSelected, selectedMarkers) {
                console.log(index, isSelected, selectedMarkers);
            },

            // -------- Labels --------
            labels: {
                markers: {
                    render: function(marker) {
                        return marker.name
                    },
                },
            },

            // -------- Marker and label style --------
            markers: markers,
            markerStyle: {
                hover: {
                    stroke: "#DDD",
                    strokeWidth: 3,
                    fill: '#FFF'
                },
                selected: {
                    fill: '#ff525d'
                }
            },
            markerLabelStyle: {
                initial: {
                    fontFamily: 'Poppins',
                    fontSize: 13,
                    fontWeight: 500,
                    fill: '#35373e',
                },
            },
        })
    </script>

@endpush
