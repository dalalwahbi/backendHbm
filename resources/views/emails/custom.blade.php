<x-mail::message>
<img src="{{ asset('images/logo.png') }}" alt="Hbmdigital Logo" style="width: 100px;">

# Hello, {{ $userName }}!

Thank you for registering with **Hbmdigital**. We are excited to have you as part of our community.

<x-mail::button :url="'https://hbmdigital.com/verify?email=' . $userName">
Verify Email
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
