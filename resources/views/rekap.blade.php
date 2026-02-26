<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekap Perizinan</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; color: #334155; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; min-width: 240px; background: #1e293b; color: #e2e8f0; transition: transform 0.2s; }
        .sidebar-brand { padding: 20px; font-weight: 600; font-size: 16px; border-bottom: 1px solid #334155; }
        .sidebar-nav { padding: 16px 0; }
        .sidebar-nav a { display: flex; align-items: center; padding: 10px 20px; color: #94a3b8; text-decoration: none; }
        .sidebar-nav a:hover { background: #334155; color: #fff; }
        .nav-icon { width: 18px; height: 18px; margin-right: 10px; opacity: 0.85; flex-shrink: 0; }
        .sidebar-nav a.active { background: #2d5a87; color: #fff; }
        .sidebar-toggle { display: none; position: fixed; top: 16px; left: 16px; z-index: 100; padding: 8px 12px; background: #1e293b; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .header { padding: 16px 24px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 600; }
        .export-btns { display: flex; gap: 8px; }
        .btn-export { padding: 8px 14px; border: 1px solid #e2e8f0; background: #fff; border-radius: 6px; font-size: 13px; cursor: pointer; }
        .btn-export:hover { background: #f1f5f9; }
        .btn-logout { padding: 8px 16px; background: #f1f5f9; border: 1px solid #e2e8f0; font-size: 13px; cursor: pointer; text-decoration: none; color: #334155; border-radius: 6px; }
        .btn-logout:hover { background: #e2e8f0; }
        .content { flex: 1; padding: 16px; overflow-x: auto; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 16px; }
        .filter-form { padding: 16px; display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group label { font-size: 12px; color: #64748b; }
        .filter-group input { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; min-width: 140px; }
        .btn-filter { padding: 8px 16px; background: #2d5a87; color: #fff; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; }
        .btn-filter:hover { background: #1e3a5f; }
        .loading { padding: 48px; text-align: center; color: #64748b; }
        .error { padding: 16px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; margin-bottom: 16px; border-radius: 6px; }
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
        th { background: #f8fafc; font-size: 11px; font-weight: 600; color: #475569; }
        tr:hover td { background: #f8fafc; }
        .empty { padding: 48px; text-align: center; color: #64748b; }
        .pagination { padding: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .pagination-info { font-size: 13px; color: #64748b; }
        .pagination-btns { display: flex; gap: 8px; }
        .pagination-btns button { padding: 6px 12px; border: 1px solid #e2e8f0; background: #fff; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .pagination-btns button:hover:not(:disabled) { background: #f1f5f9; }
        .pagination-btns button:disabled { opacity: 0.5; cursor: not-allowed; }
        .mobile-cards { display: none; }
        .mobile-card { padding: 16px; border-bottom: 1px solid #e2e8f0; }
        .mobile-card-row { display: flex; justify-content: space-between; padding: 6px 0; }
        .mobile-card-label { font-size: 11px; color: #64748b; }
        .mobile-card-value { font-weight: 500; }
        @media (max-width: 900px) {
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 90; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-toggle { display: block; }
            .header { padding-left: 56px; }
        }
        @media (max-width: 768px) {
            .table-wrap { display: none; }
            .mobile-cards { display: block; }
            .filter-form { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
    <button class="sidebar-toggle" id="sidebarToggle" type="button">☰</button>
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">Malang Alizzah</div>
            <nav class="sidebar-nav">
                <a href="{{ url('/dashboard/list-perizinan') }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    List Perizinan
                </a>
                <a href="{{ url('/dashboard/rekap') }}" class="active">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Rekap
                </a>
            </nav>
        </aside>
        <main class="main">
            <header class="header">
                <h1>Rekap Perizinan</h1>
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="export-btns" id="exportBtns" style="display:none">
                        <button type="button" class="btn-export" id="btnExportPdf">Export PDF</button>
                        <button type="button" class="btn-export" id="btnExportXls">Export XLS</button>
                    </div>
                    <a href="{{ url('/login') }}" class="btn-logout" onclick="sessionStorage.clear();">Keluar</a>
                </div>
            </header>
            <div class="content">
                <div class="card">
                    <div class="filter-form">
                        <div class="filter-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" id="tglMulai">
                        </div>
                        <div class="filter-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" id="tglSelesai">
                        </div>
                        <div class="filter-group">
                            <label>Nama</label>
                            <input type="text" id="qNama" placeholder="Cari nama...">
                        </div>
                        <div class="filter-group">
                            <label>NIS</label>
                            <input type="text" id="qNo" placeholder="Cari NIS...">
                        </div>
                        <div class="filter-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn-filter" id="btnFilter">Filter</button>
                        </div>
                    </div>
                </div>
                <div class="loading" id="loading">Memuat data...</div>
                <div class="error" id="error" style="display:none"></div>
                <div id="pageContent" class="card"></div>
            </div>
        </main>
    </div>
    <script>
        const columnLabels = {
            idincrement: 'No', nocust: 'No. Induk', ketizin: 'Keterangan Izin', kodeizin: 'Kode Izin', jenisizin: 'Jenis Izin',
            isdone: 'Status', datein: 'Tanggal Masuk', dateout: 'Tanggal Keluar', dateexp: 'Tanggal Berakhir',
            useradmin: 'Admin', result: 'Hasil', nmcust: 'Nama Siswa', dateaction: 'Tanggal Aksi', dayscount: 'Jumlah Hari',
            unit: 'Unit', approval1: 'Approval 1', approval1id: 'ID Approval 1', approval2: 'Approval 2', approval2id: 'ID Approval 2'
        };

        document.getElementById('sidebarToggle').onclick = () => document.getElementById('sidebar').classList.toggle('open');

        const unit = sessionStorage.getItem('unit');
        const token = sessionStorage.getItem('token');
        const loginUrl = "{{ url('/login') }}";
        const apiUrl = "{{ url('/api/rekap-perizinan') }}";
        if (!unit || !token) {
            window.location.href = loginUrl;
        } else {
            loadRekap(1);
        }

        document.getElementById('btnFilter').onclick = () => loadRekap(1);

        async function loadRekap(page) {
            const loading = document.getElementById('loading');
            const errorEl = document.getElementById('error');
            const content = document.getElementById('pageContent');
            loading.style.display = 'block';
            errorEl.style.display = 'none';
            content.innerHTML = '';

            const payload = {
                unit: unit,
                token: token,
                q_nama: document.getElementById('qNama').value.trim(),
                q_no: document.getElementById('qNo').value.trim(),
                tglMulai: document.getElementById('tglMulai').value.trim(),
                tglSelesai: document.getElementById('tglSelesai').value.trim(),
                page: page
            };

            try {
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                loading.style.display = 'none';

                if (data.status == 200 && data.data !== undefined) {
                    renderTable(data.data, data.meta || {});
                } else {
                    errorEl.textContent = data.message || 'Gagal memuat rekap';
                    errorEl.style.display = 'block';
                }
            } catch (e) {
                loading.style.display = 'none';
                errorEl.textContent = 'Terjadi kesalahan koneksi';
                errorEl.style.display = 'block';
            }
        }

        function getColumnLabel(key) {
            return columnLabels[String(key).toLowerCase()] || key;
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

        let currentExportData = { list: [], keys: [] };

        function renderTable(list, meta) {
            const content = document.getElementById('pageContent');
            const exportBtns = document.getElementById('exportBtns');
            list = Array.isArray(list) ? list : [];

            let keys = list.length ? Object.keys(list[0] || {}) : [];
            keys = keys.filter(k => k.toLowerCase() !== 'custid');
            currentExportData = { list, keys };

            if (list.length === 0) {
                content.innerHTML = '<div class="empty">Tidak ada data</div>';
                exportBtns.style.display = 'none';
                return;
            }
            exportBtns.style.display = 'flex';

            let tableHtml = '<div class="table-wrap"><table><thead><tr>';
            keys.forEach(k => tableHtml += '<th>' + getColumnLabel(k) + '</th>');
            tableHtml += '</tr></thead><tbody>';

            let cardsHtml = '<div class="mobile-cards">';
            list.forEach(row => {
                tableHtml += '<tr>';
                keys.forEach(k => {
                    tableHtml += '<td>' + formatVal(row[k], k) + '</td>';
                });
                tableHtml += '</tr>';

                cardsHtml += '<div class="mobile-card">';
                keys.forEach(k => {
                    cardsHtml += '<div class="mobile-card-row"><span class="mobile-card-label">' + getColumnLabel(k) + '</span><span class="mobile-card-value">' + formatVal(row[k], k) + '</span></div>';
                });
                cardsHtml += '</div>';
            });
            tableHtml += '</tbody></table></div>';

            const page = meta.page || 1;
            const totalPages = meta.totalPages || 1;
            const menampilkan = meta.menampilkan || '';

            content.innerHTML = tableHtml + cardsHtml + '<div class="pagination"><span class="pagination-info">' + menampilkan + '</span><div class="pagination-btns"><button onclick="loadRekap(' + (page - 1) + ')" ' + (page <= 1 ? 'disabled' : '') + '>Sebelumnya</button><button onclick="loadRekap(' + (page + 1) + ')" ' + (page >= totalPages ? 'disabled' : '') + '>Selanjutnya</button></div></div>';
        }

        document.getElementById('btnExportPdf').onclick = function() {
            const { list, keys } = currentExportData;
            if (!list.length) return;
            const headers = keys.map(k => getColumnLabel(k));
            const rows = list.map(row => keys.map(k => formatVal(row[k], k)));
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape' });
            doc.autoTable({
                head: [headers],
                body: rows,
                styles: { fontSize: 8 }
            });
            doc.save('rekap-perizinan-' + new Date().toISOString().slice(0,10) + '.pdf');
        };

        document.getElementById('btnExportXls').onclick = function() {
            const { list, keys } = currentExportData;
            if (!list.length) return;
            const headers = keys.map(k => getColumnLabel(k));
            const rows = list.map(row => keys.map(k => {
                const v = formatVal(row[k], k);
                return String(v).includes(',') || String(v).includes('"') ? '"' + String(v).replace(/"/g, '""') + '"' : v;
            }));
            const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'rekap-perizinan-' + new Date().toISOString().slice(0,10) + '.xls';
            a.click();
            URL.revokeObjectURL(a.href);
        };
    </script>
</body>
</html>
