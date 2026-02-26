<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Perizinan</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: #f5f5f4;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
            font-size: 15px;
            color: #1c1917;
        }
        .header {
            padding: 16px 24px;
            background: #fff;
            border-bottom: 1px solid #e7e5e4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        .logout {
            padding: 6px 14px;
            background: transparent;
            border: 1px solid #d6d3d1;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            color: #1c1917;
        }
        .logout:hover {
            background: #f5f5f4;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px;
        }
        .loading {
            padding: 48px;
            text-align: center;
            color: #78716c;
        }
        .error {
            padding: 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid #e7e5e4;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e7e5e4;
        }
        th {
            background: #fafaf9;
            font-size: 12px;
            font-weight: 600;
            color: #57534e;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafaf9; }
        .empty {
            padding: 48px;
            text-align: center;
            color: #78716c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>List Perizinan</h1>
        <a href="{{ url('/login') }}" class="logout" onclick="sessionStorage.clear();">Keluar</a>
    </div>
    <div class="container">
        <div class="loading" id="loading">Memuat data...</div>
        <div class="error" id="error" style="display:none"></div>
        <div id="content"></div>
    </div>
    <script>
        const unit = sessionStorage.getItem('unit');
        const loginUrl = "{{ url('/login') }}";
        const apiUrl = "{{ url('/api/list-perizinan') }}";
        if (!unit) {
            window.location.href = loginUrl;
        } else {
            loadPerizinan();
        }

        async function loadPerizinan() {
            const loading = document.getElementById('loading');
            const errorEl = document.getElementById('error');
            const content = document.getElementById('content');

            try {
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ unit: parseInt(unit, 10) })
                });

                const data = await res.json();
                loading.style.display = 'none';

                if (data.status === 200 && data.data) {
                    renderTable(data.data);
                } else {
                    errorEl.textContent = data.message || 'Gagal memuat data';
                    errorEl.style.display = 'block';
                }
            } catch (e) {
                loading.style.display = 'none';
                errorEl.textContent = 'Terjadi kesalahan koneksi';
                errorEl.style.display = 'block';
            }
        }

        function renderTable(data) {
            const content = document.getElementById('content');
            const list = Array.isArray(data) ? data : (data.data || data.list || [data]);

            if (list.length === 0) {
                content.innerHTML = '<div class="empty">Tidak ada data perizinan</div>';
                return;
            }

            const keys = Object.keys(typeof list[0] === 'object' ? list[0] : {});
            if (keys.length === 0) {
                content.innerHTML = '<div class="empty">Tidak ada data perizinan</div>';
                return;
            }

            let html = '<table><thead><tr>';
            keys.forEach(k => {
                html += '<th>' + k + '</th>';
            });
            html += '</tr></thead><tbody>';

            list.forEach(row => {
                html += '<tr>';
                keys.forEach(k => {
                    const val = row[k];
                    html += '<td>' + (val !== null && val !== undefined ? val : '-') + '</td>';
                });
                html += '</tr>';
            });
            html += '</tbody></table>';
            content.innerHTML = html;
        }
    </script>
</body>
</html>
