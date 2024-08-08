<div class="card custom-card text-fixed-white">
    <div class="card-body p-3">
        <div class="text-center">
            <p class="fs-14 fw-semibold mb-2">Current Week Remaining Time</p>
            <div class="d-flex align-items-center justify-content-center flex-wrap mb-2">
                <div class="clock" id="countdown-timer">
                    <div class="clock-segment">
                        <span class="clock-number" id="days">00</span>
                        <span class="clock-label">Days</span>
                    </div>
                    <div class="clock-segment">
                        <span class="clock-number" id="hours">00</span>
                        <span class="clock-label">Hours</span>
                    </div>
                    <div class="clock-segment">
                        <span class="clock-number" id="minutes">00</span>
                        <span class="clock-label">Minutes</span>
                    </div>
                    <div class="clock-segment">
                        <span class="clock-number" id="seconds">00</span>
                        <span class="clock-label">Seconds</span>
                    </div>
                </div>
            </div>
            <div class="flex-fill">
                <div class="d-flex justify-content-between">
                    <small>Rank Advancement period</small>
                    {{-- <h6 class="mb-0">Week 2</h6> --}}
                </div>
                <div class="progress progress-xs">
                    <div class="progress-bar bg-success" role="progressbar" id="progress-bar" style="width: 0%"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
