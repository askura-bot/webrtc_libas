<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kredensial</title>
    @vite(['resources/css/app.css'])
</head>
<body style="background:#0f0f0f; color:#fff; font-family:sans-serif; margin:0; padding:0;">

    {{-- Navbar --}}
    <div style="background:#1a1a2e; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #333;">
        <span style="font-size:18px; font-weight:bold;">🖥️ Command Center</span>
        <div style="display:flex; gap:12px; align-items:center;">
            <a href="{{ route('admin.dashboard') }}"
               style="color:#93c5fd; text-decoration:none; font-size:14px;">
                ← Kembali ke Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="padding:8px 16px; background:#dc2626; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Main --}}
    <div style="max-width:700px; margin:40px auto; padding:0 20px;">

        <h2 style="margin-bottom:6px;">🔑 Kelola Kredensial</h2>
        <p style="color:#6b7280; margin-bottom:32px; font-size:14px;">
            Kosongkan field password jika tidak ingin mengubah password.
        </p>

        {{-- Form Officer --}}
        <div style="background:#1a1a1a; border:1px solid #333; border-radius:10px; padding:24px; margin-bottom:24px;">
            <h3 style="margin:0 0 20px 0; color:#fbbf24; font-size:16px;">👮 Kredensial Petugas</h3>

            @if(session('success_officer'))
                <div style="background:#14532d; color:#4ade80; padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:14px;">
                    ✅ {{ session('success_officer') }}
                </div>
            @endif

            @if($errors->hasBag('default'))
                <div style="background:#7f1d1d; color:#fca5a5; padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.credentials.officer') }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-size:14px; color:#d1d5db;">
                        Email Petugas
                    </label>
                    <input type="email" name="officer_email"
                           value="{{ old('officer_email', $officer->email) }}"
                           required
                           style="width:100%; padding:10px 12px; background:#111; border:1px solid #444;
                           border-radius:6px; color:#fff; font-size:14px; box-sizing:border-box;">
                    @error('officer_email')
                        <span style="color:#f87171; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-size:14px; color:#d1d5db;">
                        Password Baru <span style="color:#6b7280;">(opsional)</span>
                    </label>
                    <input type="password" name="officer_password"
                           placeholder="Kosongkan jika tidak ingin mengubah"
                           style="width:100%; padding:10px 12px; background:#111; border:1px solid #444;
                           border-radius:6px; color:#fff; font-size:14px; box-sizing:border-box;">
                    @error('officer_password')
                        <span style="color:#f87171; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:6px; font-size:14px; color:#d1d5db;">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" name="officer_password_confirmation"
                           placeholder="Ulangi password baru"
                           style="width:100%; padding:10px 12px; background:#111; border:1px solid #444;
                           border-radius:6px; color:#fff; font-size:14px; box-sizing:border-box;">
                </div>

                <button type="submit"
                        style="width:100%; padding:12px; background:#1d4ed8; color:#fff; border:none;
                        border-radius:6px; font-size:15px; cursor:pointer; font-weight:bold;">
                    Simpan Kredensial Petugas
                </button>
            </form>
        </div>

        {{-- Form Admin --}}
        <div style="background:#1a1a1a; border:1px solid #333; border-radius:10px; padding:24px; margin-bottom:40px;">
            <h3 style="margin:0 0 20px 0; color:#818cf8; font-size:16px;">🛡️ Kredensial Admin</h3>

            @if(session('success_admin'))
                <div style="background:#14532d; color:#4ade80; padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:14px;">
                    ✅ {{ session('success_admin') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.credentials.admin') }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-size:14px; color:#d1d5db;">
                        Email Admin
                    </label>
                    <input type="email" name="admin_email"
                           value="{{ old('admin_email', $admin->email) }}"
                           required
                           style="width:100%; padding:10px 12px; background:#111; border:1px solid #444;
                           border-radius:6px; color:#fff; font-size:14px; box-sizing:border-box;">
                    @error('admin_email')
                        <span style="color:#f87171; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-size:14px; color:#d1d5db;">
                        Password Baru <span style="color:#6b7280;">(opsional)</span>
                    </label>
                    <input type="password" name="admin_password"
                           placeholder="Kosongkan jika tidak ingin mengubah"
                           style="width:100%; padding:10px 12px; background:#111; border:1px solid #444;
                           border-radius:6px; color:#fff; font-size:14px; box-sizing:border-box;">
                    @error('admin_password')
                        <span style="color:#f87171; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:6px; font-size:14px; color:#d1d5db;">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" name="admin_password_confirmation"
                           placeholder="Ulangi password baru"
                           style="width:100%; padding:10px 12px; background:#111; border:1px solid #444;
                           border-radius:6px; color:#fff; font-size:14px; box-sizing:border-box;">
                </div>

                <button type="submit"
                        style="width:100%; padding:12px; background:#4f46e5; color:#fff; border:none;
                        border-radius:6px; font-size:15px; cursor:pointer; font-weight:bold;">
                    Simpan Kredensial Admin
                </button>
            </form>
        </div>

    </div>

</body>
</html>