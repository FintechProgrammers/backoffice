<form method="POST" action="{{ route('admin.streamers.store') }}">
    @csrf
    @include('admin.streamer._form')
</form>
