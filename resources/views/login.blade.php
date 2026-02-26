<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 15px;
            color: #333;
        }
        .login-wrap {
            width: 100%;
            max-width: 420px;
            padding: 24px;
        }
        .login-box {
            background: #fff;
            padding: 40px 36px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            border-radius: 16px;
        }
        .login-box h1 {
            margin: 0 0 8px;
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
        }
        .login-box .subtitle {
            margin: 0 0 32px;
            font-size: 14px;
            color: #6b7280;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #2d5a87;
            box-shadow: 0 0 0 3px rgba(45,90,135,0.15);
        }
        .turnstile-wrap { margin: 16px 0; min-height: 65px; }
        button {
            width: 100%;
            padding: 14px;
            margin-top: 16px;
            background: #1e3a5f;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
        }
        button:hover { background: #2d5a87; }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <h1>Login</h1>
            <p class="subtitle">Masuk ke akun Anda</p>
            <form id="loginForm">
                @csrf
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus placeholder="Masukkan username">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">

                <div class="turnstile-wrap">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.sitekey') }}"></div>
                </div>

                <button type="submit" id="btnSubmit">Masuk</button>
            </form>
        </div>
    </div>
    <script>
        const form = document.getElementById('loginForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const apiUrl = "{{ url('/api/login') }}";
        const redirectUrl = "{{ url('/dashboard/list-perizinan') }}";

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnSubmit.disabled = true;

            const turnstileResp = document.querySelector('[name="cf-turnstile-response"]')?.value;
            if (!turnstileResp) {
                btnSubmit.disabled = false;
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Selesaikan verifikasi terlebih dahulu.' });
                return;
            }
            if (!document.getElementById('username').value.trim()) {
                btnSubmit.disabled = false;
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Username wajib diisi.' });
                return;
            }
            if (!document.getElementById('password').value) {
                btnSubmit.disabled = false;
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Password wajib diisi.' });
                return;
            }

            try {
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        username: document.getElementById('username').value.trim(),
                        password: document.getElementById('password').value,
                        cf_turnstile_response: document.querySelector('[name="cf-turnstile-response"]')?.value || ''
                    })
                });

                let data;
                try {
                    data = await res.json();
                } catch (e) {
                    throw new Error('Response tidak valid. Cek apakah route /api/login dapat diakses.');
                }
                const unit = data.data?.unit ?? data.unit;

                if (data.status == 200 && (unit !== undefined && unit !== null)) {
                    sessionStorage.setItem('unit', unit);
                    sessionStorage.setItem('user', JSON.stringify(data.data || {}));
                    if (data.token) {
                        sessionStorage.setItem('token', data.token);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Login berhasil',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = redirectUrl;
                    });
                } else {
                    if (typeof turnstile !== 'undefined') turnstile.reset();
                    btnSubmit.disabled = false;
                    let errMsg = data.message || 'Login gagal';
                    if (data.status == 200 && (unit === undefined || unit === null)) {
                        errMsg = 'Data login tidak lengkap. Unit tidak ditemukan dalam respons.';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errMsg
                    });
                }
            } catch (err) {
                if (typeof turnstile !== 'undefined') turnstile.reset();
                btnSubmit.disabled = false;
                const msg = err.message || 'Terjadi kesalahan koneksi';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    </script>
</body>
</html>
