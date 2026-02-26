<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
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
            padding: 48px 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border-radius: 12px;
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
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            background: #f9fafb;
            transition: border-color 0.2s, background 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #2d5a87;
            background: #fff;
        }
        button {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
        }
        button:hover { opacity: 0.95; }
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

            try {
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value
                    })
                });

                const data = await res.json();
                const unit = data.data?.unit ?? data.unit;

                if (data.status == 200 && (unit !== undefined && unit !== null)) {
                    sessionStorage.setItem('unit', unit);
                    sessionStorage.setItem('user', JSON.stringify(data.data || {}));
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
                    btnSubmit.disabled = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Login gagal'
                    });
                }
            } catch (err) {
                btnSubmit.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan koneksi'
                });
            }
        });
    </script>
</body>
</html>
