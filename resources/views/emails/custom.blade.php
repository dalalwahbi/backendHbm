@component('mail::message')
# Hello {{ $userName }},

Your account has been successfully verified!

Your custom ID is: **{{ $customId }}**

Thank you for joining us!

@component('mail::button', ['url' => $verificationUrl])
Verify Your Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
