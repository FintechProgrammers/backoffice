<form method="POST" action="{{ route('admin.assets.update', $asset->uuid) }}">
    @csrf
    @include('admin.assets._form')
</form>
