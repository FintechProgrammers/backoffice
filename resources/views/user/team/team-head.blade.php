<a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"
    class="show-detail" data-url="{{ route('team.user.info', $user->uuid) }}">
    <div class="tree-img" style="background-image: url('{{ $user->profile_picture }}')">
    </div>
    {{ $user->username }}
</a>
