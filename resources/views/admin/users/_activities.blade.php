@if ($user->activities->count() > 0)
    <ul class="list-unstyled profile-timeline">
        @foreach ($user->activities as $item)
            <li id="profile-posts-scroll">
                <div>
                    <span class="avatar avatar-sm bg-primary-transparent avatar-rounded profile-timeline-avatar">
                       <i class="bx bx-circle"></i>
                    </span>
                    <p class="mb-0">
                        {{ $item->log }}
                        <span class="float-end fs-11 text-muted">{{ $item->created_at->format('jS,M Y H:i A') }}</span>
                    </p>
                    {{-- <p class="text-muted mb-0">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae, repellendus rem rerum
                        excepturi aperiam ipsam temporibus inventore ullam tempora eligendi libero sequi dignissimos
                        cumque,
                        et a sint tenetur consequatur omnis!
                    </p> --}}
                </div>
            </li>
        @endforeach
    </ul>
@else
    <div class="d-flex justify-content-center">
        <h6 class="text-warning">no subscriptions</h6>
    </div>
@endif
