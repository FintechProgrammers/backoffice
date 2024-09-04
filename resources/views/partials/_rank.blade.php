 <div class="me-2">
     @if (!empty($user->rank))
         <span class="avatar avatar-rounded">
             <img src="{{ $user->rank->file_url }}" alt="img">
         </span>
     @else
         <span class="avatar avatar-md avatar-rounded bg-white text-dark shadow-sm">
             <img src="{{ asset('assets/images/pin-sin-rango.png') }}" alt="img">
         </span>
     @endif
 </div>

 <div class="flex-fill">
     <h3 class="fw-semibold mb-1 text-fixed-white">
         @if (!empty($user->rank))
             <span>{{ $user->rank->name }}</span>
         @else
             <small>no rank</small>
         @endif
     </h3>
 </div>
