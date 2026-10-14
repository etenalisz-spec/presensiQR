<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Presensi QR Universitas Pamulang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --unpam-blue: #0088EA;
            --unpam-dark-blue: #0066B3;
            --bg-body: #F4F7FB;
            --surface: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-line: #E2E8F0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background-color: var(--bg-body);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border-line);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 136, 234, 0.08);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, var(--unpam-blue), #00A3FF);
            color: #ffffff;
            padding: 32px 28px;
            text-align: center;
        }
        .login-header h2 { font-size: 20px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
        .login-header p { font-size: 13.5px; opacity: 0.9; margin-top: 6px; }
        .login-body { padding: 32px 28px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155; }
        .form-control {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1.5px solid var(--border-line);
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.15s;
        }
        .form-control:focus { outline: none; border-color: var(--unpam-blue); }
        .btn-submit {
            width: 100%;
            height: 46px;
            background: var(--unpam-blue);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
            margin-top: 8px;
        }
        .btn-submit:hover { background: var(--unpam-dark-blue); }
        .alert-error {
            background: #FDEBE9;
            color: #C2352B;
            border: 1px solid #F8C3BD;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h2>Universitas Pamulang</h2>
            <p>Sistem Presensi Mahasiswa Berbasis QR Code</p>
        </div>
        <div class="login-body">
            @if($errors->has('login'))
                <div class="alert-error">{{ $errors->first('login') }}</div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username">NIM / NIDN / Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Contoh: 2211001" value="{{ old('username') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative">
                        <input type="password" id="password" name="password" class="form-control" style="padding-right: 44px" placeholder="Masukkan password" required>
                        <button type="button" id="toggle-pwd-btn" onclick="togglePasswordVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748B; padding: 4px; display: flex; align-items: center" title="Lihat / Sembunyikan Password">
                            <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Masuk ke Sistem</button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                pwdInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
</body>
</html>
