<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login — Sistem Polisi</title>
    @vite(['resources/css/app.css'])
</head>
<body style="background:#1a1a2e; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; font-family:sans-serif;">

    <div style="background:#fff; padding:40px; border-radius:10px; width:100%; max-width:400px;">
        <h2 style="text-align:center; margin-bottom:24px;">🚔 Sistem Live Polisi</h2>

        @if($errors->any())
            <div style="background:#fee2e2; color:#dc2626; padding:10px; border-radius:6px; margin-bottom:16px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:6px; font-weight:600;">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; margin-bottom:6px; font-weight:600;">Password</label>
                <input type="password" name="password" required
                       style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
            </div>

            <button type="submit"
                    style="width:100%; padding:12px; background:#1d4ed8; color:#fff; border:none; border-radius:6px; font-size:16px; cursor:pointer;">
                Login
            </button>
        </form>
    </div>

</body>
</html>