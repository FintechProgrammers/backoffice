<form method="POST" action="{{ route('admin.assets.store') }}">
    @csrf
    @include('admin.assets._form')
</form>
