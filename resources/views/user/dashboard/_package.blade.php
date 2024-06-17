@if (!empty($services))
    <div class="card custom-card" style="background: transparent">
        <div class="card-body ">
            <div class="swiper swiper-navigation" style="height: 250px">
                <div class="swiper-wrapper">
                    @foreach ($services as $item)
                    <div class="swiper-slide"><img style="object-fit: cover"
                        src="{{ $item->image_url }}"
                        alt=""></div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
@endif
