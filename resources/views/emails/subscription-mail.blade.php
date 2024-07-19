<x-mail::message>
    # {{ $data['name'] }}

    {{ $data['content'] }}

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
