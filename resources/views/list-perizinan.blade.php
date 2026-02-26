<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Perizinan</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 14px;
            color: #334155;
        }
        .layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            min-width: 240px;
            background: #1e293b;
            color: #e2e8f0;
            transition: transform 0.2s, margin 0.2s;
        }
        .sidebar-brand {
            padding: 20px;
            font-weight: 600;
            font-size: 16px;
            border-bottom: 1px solid #334155;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-brand img { height: 32px; }
        .sidebar-nav {
            padding: 16px 0;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #94a3b8;
            text-decoration: none;
        }
        .sidebar-nav a:hover {
            background: #334155;
            color: #fff;
        }
        .sidebar-nav a.active {
            background: #2d5a87;
            color: #fff;
        }
        .nav-icon { width: 18px; height: 18px; margin-right: 10px; opacity: 0.85; flex-shrink: 0; }
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 100;
            padding: 8px 12px;
            background: #1e293b;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .header {
            padding: 16px 24px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        .btn-logout {
            padding: 8px 16px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            color: #334155;
            border-radius: 6px;
        }
        .btn-logout:hover {
            background: #e2e8f0;
        }
        .content {
            flex: 1;
            padding: 16px;
            overflow-x: auto;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .loading {
            padding: 48px;
            text-align: center;
            color: #64748b;
        }
        .error {
            padding: 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            margin-bottom: 16px;
            border-radius: 6px;
        }
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-wrap::-webkit-scrollbar {
            height: 8px;
        }
        .table-wrap::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        th {
            background: #f8fafc;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }
        .empty {
            padding: 48px;
            text-align: center;
            color: #64748b;
        }
        .mobile-cards {
            display: none;
        }
        .mobile-card {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .mobile-card:last-child {
            border-bottom: none;
        }
        .mobile-card-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }
        .mobile-card-label {
            font-size: 11px;
            color: #64748b;
        }
        .mobile-card-value {
            font-weight: 500;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 4px;
        }
        .btn-approve {
            background: #dcfce7;
            color: #16a34a;
        }
        .btn-approve:hover {
            background: #22c55e;
            color: #fff;
        }
        .btn-reject {
            background: #fee2e2;
            color: #dc2626;
        }
        .btn-reject:hover {
            background: #ef4444;
            color: #fff;
        }
        @media (max-width: 900px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 90;
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-toggle {
                display: block;
            }
            .main {
                margin-left: 0;
            }
            .header {
                padding-left: 56px;
            }
        }
        @media (max-width: 768px) {
            .table-wrap { display: none; }
            .mobile-cards { display: block; }
        }
    </style>
</head>
<body>
    <button class="sidebar-toggle" id="sidebarToggle" type="button">☰</button>
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                <span>Malang Alizah</span>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ url('/dashboard/list-perizinan') }}" class="active">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    List Perizinan
                </a>
                <a href="{{ url('/dashboard/rekap') }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Rekap
                </a>
                <a href="{{ url('/dashboard/users') }}" id="userMenuLink" style="display:none">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    User
                </a>
            </nav>
        </aside>
        <main class="main">
            <header class="header">
                <h1>List Perizinan</h1>
                <a href="{{ url('/login') }}" class="btn-logout" onclick="sessionStorage.clear();">Keluar</a>
            </header>
            <div class="content">
                <div class="loading" id="loading">Memuat data...</div>
                <div class="error" id="error" style="display:none"></div>
                <div id="pageContent" class="card"></div>
            </div>
        </main>
    </div>
    <script>
        const columnLabels = {
            idincrement: 'No',
            custid: 'ID Pelanggan',
            nocust: 'No. Induk',
            ketizin: 'Keterangan Izin',
            kodeizin: 'Kode Izin',
            jenisizin: 'Jenis Izin',
            isdone: 'Status',
            datein: 'Tanggal Masuk',
            dateout: 'Tanggal Keluar',
            dateexp: 'Tanggal Berakhir',
            useradmin: 'Admin',
            result: 'Hasil',
            nmcust: 'Nama Siswa',
            dateaction: 'Tanggal Aksi',
            dayscount: 'Jumlah Hari',
            unit: 'Unit',
            approval1: 'Approval 1',
            approval1id: 'ID Approval 1',
            approval2: 'Approval 2',
            approval2id: 'ID Approval 2'
        };

        document.getElementById('sidebarToggle').onclick = function() {
            document.getElementById('sidebar').classList.toggle('open');
        };

        const unit = sessionStorage.getItem('unit');
        const token = sessionStorage.getItem('token');
        const userStr = sessionStorage.getItem('user');
        const user = userStr ? JSON.parse(userStr) : {};
        const idUser = user.idincrement ?? user.IDINCREMENT;
        const loginUrl = "{{ url('/login') }}";
        const apiUrl = "{{ url('/api/list-perizinan') }}";
        const approveUrl = "{{ url('/api/approve-perizinan') }}";
        if (!unit || !token) {
            window.location.href = loginUrl;
        } else {
            const role = (user.role || user.Role || '').toLowerCase();
            if (role === 'superadmin') {
                const link = document.getElementById('userMenuLink');
                if (link) link.style.display = 'flex';
            }
            loadPerizinan();
        }

        async function loadPerizinan() {
            const loading = document.getElementById('loading');
            const errorEl = document.getElementById('error');
            const content = document.getElementById('pageContent');

            try {
                const token = sessionStorage.getItem('token');
                if (!token) {
                    Swal.fire({ icon: 'error', title: 'Session habis', text: 'Silakan login ulang' }).then(() => { sessionStorage.clear(); window.location.href = loginUrl; });
                    return;
                }
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ unit: unit, token: token })
                });

                const data = await res.json();
                loading.style.display = 'none';

                const listData = data.data ?? data.perizinan ?? data.list ?? (Array.isArray(data) ? data : null);
                if (data.status == 200 && listData !== undefined && listData !== null) {
                    renderTable(listData);
                } else {
                    errorEl.textContent = data.message || 'Gagal mendapatkan data perizinan';
                    errorEl.style.display = 'block';
                }
            } catch (e) {
                loading.style.display = 'none';
                errorEl.textContent = 'Terjadi kesalahan koneksi. ' + (e.message || '');
                errorEl.style.display = 'block';
            }
        }

        function getColumnLabel(key) {
            const k = String(key).toLowerCase();
            return columnLabels[k] || key;
        }

        function getRowVal(row, key) {
            const k = Object.keys(row).find(x => x.toLowerCase() === key.toLowerCase());
            return k ? row[k] : undefined;
        }

        function formatVal(val, key) {
            if (val === null || val === undefined) return '-';
            const k = String(key).toLowerCase();
            if (k === 'isdone') return val == 1 ? 'Selesai' : 'Belum';
            if (k === 'approval1' || k === 'approval2') {
                if (val == 0 || val === '0') return 'Belum';
                if (val == 1 || val === '1') return 'Diterima';
                if (val == 2 || val === '2') return 'Ditolak';
            }
            return val;
        }

        async function doApprove(nmcust, dateAction, status) {
            if (!nmcust || !dateAction) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data tidak lengkap' });
                return;
            }
            if (!idUser) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Session tidak valid, silakan login ulang' });
                return;
            }
            try {
                const token = sessionStorage.getItem('token');
                if (!token) {
                    Swal.fire({ icon: 'error', title: 'Session habis', text: 'Silakan login ulang' });
                    return;
                }
                const res = await fetch(approveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        token: token,
                        id_user: parseInt(idUser, 10),
                        unit: String(unit),
                        nmcust: String(nmcust),
                        date_action: String(dateAction),
                        status: status
                    })
                });
                const data = await res.json();
                if (data.status == 200) {
                    const label = status === 1 ? 'Perizinan diterima' : 'Perizinan ditolak';
                    const appr = data.apprver == 1 ? 'Approval 1' : 'Approval 2';
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: label + ' (' + appr + ')'
                    }).then(() => loadPerizinan());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal memproses' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan koneksi' });
            }
        }

        function renderTable(data) {
            const content = document.getElementById('pageContent');
            let list = Array.isArray(data) ? data : (data.data || data.list || [data]);
            list = list.filter(row => {
                const k = Object.keys(row).find(x => x.toLowerCase() === 'isdone');
                const isdone = k ? row[k] : undefined;
                return isdone == 0 || isdone === '0';
            });

            if (list.length === 0) {
                content.innerHTML = '<div class="empty">Tidak ada data perizinan</div>';
                return;
            }

            let keys = Object.keys(typeof list[0] === 'object' ? list[0] : {});
            keys = keys.filter(k => k.toLowerCase() !== 'custid');
            if (keys.length === 0) {
                content.innerHTML = '<div class="empty">Tidak ada data perizinan</div>';
                return;
            }

            let tableHtml = '<div class="table-wrap"><table><thead><tr><th>Aksi</th>';
            keys.forEach(k => {
                tableHtml += '<th>' + getColumnLabel(k) + '</th>';
            });
            tableHtml += '</tr></thead><tbody>';

            let cardsHtml = '<div class="mobile-cards">';
            list.forEach((row, idx) => {
                const nmcust = String(getRowVal(row, 'nmcust') || '');
                const dateAction = String(getRowVal(row, 'dateaction') || '');
                const nmEsc = nmcust.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                const daEsc = dateAction.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                tableHtml += '<tr>';
                tableHtml += '<td><button type="button" class="btn-action btn-approve" title="Terima" onclick="doApprove(\'' + nmEsc + '\', \'' + daEsc + '\', 1)">✓</button><button type="button" class="btn-action btn-reject" title="Tolak" onclick="doApprove(\'' + nmEsc + '\', \'' + daEsc + '\', 2)">✗</button></td>';
                keys.forEach(k => {
                    let val = formatVal(row[k], k);
                    tableHtml += '<td>' + val + '</td>';
                });
                tableHtml += '</tr>';

                cardsHtml += '<div class="mobile-card">';
                cardsHtml += '<div class="mobile-card-row" style="margin-bottom:12px"><span class="mobile-card-label">Aksi</span><span><button type="button" class="btn-action btn-approve" onclick="doApprove(\'' + nmEsc + '\', \'' + daEsc + '\', 1)">✓ Terima</button> <button type="button" class="btn-action btn-reject" onclick="doApprove(\'' + nmEsc + '\', \'' + daEsc + '\', 2)">✗ Tolak</button></span></div>';
                keys.forEach(k => {
                    let val = formatVal(row[k], k);
                    cardsHtml += '<div class="mobile-card-row"><span class="mobile-card-label">' + getColumnLabel(k) + '</span><span class="mobile-card-value">' + val + '</span></div>';
                });
                cardsHtml += '</div>';
            });
            tableHtml += '</tbody></table></div>';
            cardsHtml += '</div>';

            content.innerHTML = tableHtml + cardsHtml;
        }
    </script>
</body>
</html>
