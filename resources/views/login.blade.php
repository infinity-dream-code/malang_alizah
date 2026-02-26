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
            background: #f5f5f4;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
            font-size: 15px;
            color: #1c1917;
        }
        .login-box {
            width: 100%;
            max-width: 360px;
            padding: 32px;
            background: #fff;
            border: 1px solid #e7e5e4;
        }
        h1 {
            margin: 0 0 28px;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.02em;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            color: #57534e;
        }
        input {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border: 1px solid #d6d3d1;
            font-size: 15px;
            font-family: inherit;
        }
        input:focus {
            outline: none;
            border-color: #78716c;
        }
        .error {
            margin-bottom: 18px;
            padding: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            font-size: 13px;
            color: #991b1b;
            display: none;
        }
        .error.show { display: block; }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            background: #1c1917;
            color: #fff;
            border: none;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
        }
        button:hover { background: #292524; }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Login</h1>
        <div class="error" id="error"></div>
        <form id="loginForm">
            @csrf
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" id="btnSubmit">Masuk</button>
        </form>
    </div>
    <script>
        const form = document.getElementById('loginForm');
        const errorEl = document.getElementById('error');
        const btnSubmit = document.getElementById('btnSubmit');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            errorEl.classList.remove('show');
            errorEl.textContent = '';
            btnSubmit.disabled = true;

            const res = await fetch('/api/login', {
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
                window.location.href = '/list-perizinan';
                return;
            }

            errorEl.textContent = data.message || 'Login gagal';
            errorEl.classList.add('show');
            btnSubmit.disabled = false;
        });
    </script>
</body>
</html>
