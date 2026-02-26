<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a1a1a;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 15px;
            color: #333;
        }
        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .login-box h1 {
            margin: 0 0 32px;
            font-size: 22px;
            font-weight: 600;
            color: #1a1a1a;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            font-size: 15px;
            font-family: inherit;
            background: #fafafa;
        }
        input:focus {
            outline: none;
            border-color: #1a1a1a;
            background: #fff;
        }
        .error {
            margin-bottom: 20px;
            padding: 12px;
            background: #fff5f5;
            border-left: 3px solid #e53e3e;
            font-size: 14px;
            color: #c53030;
            display: none;
        }
        .error.show { display: block; }
        button {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            font-size: 15px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
        }
        button:hover { background: #333; }
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
            <div class="error" id="error"></div>
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
        const errorEl = document.getElementById('error');
        const btnSubmit = document.getElementById('btnSubmit');
        const baseUrl = "{{ url('/') }}";

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            errorEl.classList.remove('show');
            errorEl.textContent = '';
            btnSubmit.disabled = true;

            const res = await fetch(baseUrl + '/api/login', {
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

            if (data.status === 200 && data.data && data.data.unit != null) {
                sessionStorage.setItem('unit', data.data.unit);
                sessionStorage.setItem('user', JSON.stringify(data.data));
                window.location.href = baseUrl + '/list-perizinan';
                return;
            }

            errorEl.textContent = data.message || 'Login gagal';
            errorEl.classList.add('show');
            btnSubmit.disabled = false;
        });
    </script>
</body>
</html>
