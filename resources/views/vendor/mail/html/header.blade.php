@props(['url'])

<tr>
    <td class="header">
        <a href="{{ $url }}">
            @if ($slot === 'Laravel')
            <img src="{{ asset('images/logo.png') }}" class="logo" alt="{{ config('app.name') }}">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
