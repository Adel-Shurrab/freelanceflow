<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'FreelanceFlow' }}</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f8fafc; color: #0f172a;">
    <main style="max-width: 720px; margin: 48px auto; padding: 24px;">
        @if (session('status'))
            <div style="padding: 12px; margin-bottom: 16px; background: #dcfce7; border: 1px solid #86efac;">
                {{ session('status') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</body>

</html>