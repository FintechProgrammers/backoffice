
<h5>Create Provider</h5>
<form action="{{ route('admin.provider.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.provider._form')
</form>
