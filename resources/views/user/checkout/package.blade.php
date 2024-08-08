<div class="p-4">
    <p class="mb-1 fw-semibold text-muted op-5 fs-20">01</p>
    <div class="fs-15 fw-semibold d-sm-flex d-block align-items-center justify-content-between mb-3">
        <div>Package :</div>
    </div>
    <div class="my-4">
        <div class="d-flex flex-wrap align-items-top justify-content-between gap-2">
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <div>
                        <span class="avatar avatar-rounded avatar-lg">
                            <img src="{{ $package->image_url }}" alt="">
                        </span>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 d-flex align-items-center"><a
                                href="javascript:void(0);">{{ $package->name }}</a></h4>
                        <a href="javascript:void(0);" class="fw-semibold"><i class="bi bi-coin"></i>
                            ${{ number_format($package->price, 2, '.', ',') }}</a>
                    </div>
                </div>
            </div>
            <div>
                <p class="mb-0"><i class="bi bi-info-circle text-danger" data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Package duration is {{ convertDaysToUnit($package->duration, $package->duration_unit) . ' ' . $package->duration_unit }}"></i>
                    <b>{{ convertDaysToUnit($package->duration, $package->duration_unit) . ' ' . $package->duration_unit }}
                    </b></p>
            </div>
        </div>
        <p class="op-9">
            {{ $package->description }}
        </p>
        @if ($package->serviceProduct->isNotEmpty())
            <div class="fs-15 fw-semibold d-sm-flex d-block align-items-center justify-content-between mb-3">
                <div>Services :</div>
            </div>
            <ol class="list-group border-0 list-unstyled list-group-numbered mb-3">
                @foreach ($package->serviceProduct as $item)
                    <li class="list-group-item border-0 py-1">{{ $item->product->name }}</li>
                @endforeach
            </ol>
        @endif

    </div>
</div>
