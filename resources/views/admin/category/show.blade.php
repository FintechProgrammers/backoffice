<form method="POST" action="{{ route('admin.category.update', $category->uuid) }}">
    @csrf
    @include('admin.category._form')
</form>
