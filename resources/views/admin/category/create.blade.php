<form method="POST" action="{{ route('admin.category.store') }}">
    @csrf
    @include('admin.category._form')
</form>
