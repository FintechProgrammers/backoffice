<form method="POST" action="{{ route('admin.streamers.update', $streamer->uuid) }}">
    @csrf
    @include('admin.streamer._form')
</form>
