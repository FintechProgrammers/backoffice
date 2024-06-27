<h5>Update Provider</h5>
<form action="{{ route('admin.provider.update', $provider->uuid) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.provider._form')
</form>
