<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola User</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; color: #334155; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; min-width: 240px; background: #1e293b; transition: transform 0.2s; }
        .sidebar-brand { padding: 20px; font-weight: 600; font-size: 16px; border-bottom: 1px solid #334155; display: flex; align-items: center; gap: 10px; }
        .sidebar-brand img { height: 32px; }
        .sidebar-nav { padding: 16px 0; }
        .sidebar-nav a { display: flex; align-items: center; padding: 10px 20px; color: #94a3b8; text-decoration: none; }
        .sidebar-nav a:hover { background: #334155; color: #fff; }
        .nav-icon { width: 18px; height: 18px; margin-right: 10px; opacity: 0.85; flex-shrink: 0; }
        .sidebar-nav a.active { background: #2d5a87; color: #fff; }
        .sidebar-toggle { display: none; position: fixed; top: 16px; left: 16px; z-index: 100; padding: 8px 12px; background: #1e293b; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .header { padding: 16px 24px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 600; }
        .btn { padding: 8px 16px; border-radius: 6px; font-size: 13px; cursor: pointer; border: 1px solid #e2e8f0; background: #fff; }
        .btn:hover { background: #f1f5f9; }
        .btn-primary { background: #2d5a87; color: #fff; border: none; }
        .btn-primary:hover { background: #1e3a5f; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        .btn-danger { color: #dc2626; }
        .btn-danger:hover { background: #fef2f2; }
        .btn-logout { padding: 8px 16px; background: #f1f5f9; border: 1px solid #e2e8f0; font-size: 13px; cursor: pointer; text-decoration: none; color: #334155; border-radius: 6px; }
        .content { flex: 1; padding: 16px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 16px; }
        .toolbar { padding: 16px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .toolbar input { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; width: 200px; }
        .loading, .empty { padding: 48px; text-align: center; color: #64748b; }
        .error { padding: 16px; background: #fef2f2; color: #991b1b; margin-bottom: 16px; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-size: 12px; font-weight: 600; color: #475569; }
        .pagination { padding: 16px; display: flex; justify-content: space-between; align-items: center; }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 200; align-items: center; justify-content: center; }
        .modal.show { display: flex; }
        .modal-content { background: #fff; border-radius: 8px; padding: 24px; min-width: 320px; max-width: 420px; width: 90%; }
        .modal-content h3 { margin: 0 0 20px; }
        .form-row { margin-bottom: 16px; }
        .form-row label { display: block; margin-bottom: 6px; font-size: 13px; color: #475569; }
        .form-row input, .form-row select { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; }
        .form-actions { margin-top: 24px; display: flex; gap: 8px; justify-content: flex-end; }
        @media (max-width: 900px) {
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 90; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-toggle { display: block; }
            .header { padding-left: 56px; }
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
                <a href="{{ url('/dashboard/list-perizinan') }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    List Perizinan
                </a>
                <a href="{{ url('/dashboard/rekap') }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Rekap
                </a>
                <a href="{{ url('/dashboard/users') }}" class="active" id="userMenuLink">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    User
                </a>
            </nav>
        </aside>
        <main class="main">
            <header class="header">
                <h1>Kelola User</h1>
                <a href="{{ url('/login') }}" class="btn-logout" onclick="sessionStorage.clear();">Keluar</a>
            </header>
            <div class="content">
                <div class="card toolbar">
                    <input type="text" id="searchQ" placeholder="Cari username, nama, unit...">
                    <button type="button" class="btn btn-primary" id="btnAdd">Tambah User</button>
                    <button type="button" class="btn" id="btnSearch">Cari</button>
                </div>
                <div class="loading" id="loading">Memuat data...</div>
                <div class="error" id="error" style="display:none"></div>
                <div id="pageContent" class="card"></div>
            </div>
        </main>
    </div>

    <div class="modal" id="modalForm">
        <div class="modal-content">
            <h3 id="modalTitle">Tambah User</h3>
            <form id="userForm">
                <input type="hidden" id="editId">
                <div class="form-row">
                    <label>Username</label>
                    <input type="text" id="fUsername" required>
                </div>
                <div class="form-row" id="passRow">
                    <label>Password</label>
                    <input type="password" id="fPassword">
                </div>
                <div class="form-row">
                    <label>Nama</label>
                    <input type="text" id="fNama" required>
                </div>
                <div class="form-row">
                    <label>Unit</label>
                    <input type="text" id="fUnit" placeholder="Opsional">
                </div>
                <div class="form-row">
                    <label>Apprver</label>
                    <select id="fApprver">
                        <option value="">-</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>Role</label>
                    <input type="text" id="fRole" placeholder="superadmin, admin, dll">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn" id="btnModalCancel">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const token = sessionStorage.getItem('token');
        const userStr = sessionStorage.getItem('user');
        const user = userStr ? JSON.parse(userStr) : {};
        const role = (user.role || user.Role || '').toLowerCase();
        const loginUrl = "{{ url('/login') }}";
        const apiBase = "{{ url('/api') }}";

        if (!token) {
            window.location.href = loginUrl;
        } else if (role !== 'superadmin') {
            window.location.href = "{{ url('/dashboard/list-perizinan') }}";
        } else {
            loadUsers(1);
        }

        document.getElementById('sidebarToggle').onclick = () => document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('btnSearch').onclick = () => loadUsers(1);
        document.getElementById('searchQ').onkeypress = (e) => e.key === 'Enter' && loadUsers(1);
        document.getElementById('btnAdd').onclick = () => openModal();
        document.getElementById('btnModalCancel').onclick = () => closeModal();
        document.getElementById('userForm').onsubmit = (e) => { e.preventDefault(); saveUser(); };

        async function loadUsers(page) {
            const loading = document.getElementById('loading');
            const errorEl = document.getElementById('error');
            const content = document.getElementById('pageContent');
            loading.style.display = 'block';
            errorEl.style.display = 'none';

            try {
                const res = await fetch(apiBase + '/users-list', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ token, q: document.getElementById('searchQ').value.trim(), page })
                });
                const data = await res.json();
                loading.style.display = 'none';

                if (data.status == 200 && data.data) {
                    renderTable(data.data, data.meta || {});
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

        function renderTable(list, meta) {
            const content = document.getElementById('pageContent');
            list = Array.isArray(list) ? list : [];
            const cols = ['idincrement', 'username', 'nama', 'unit', 'Apprver', 'role'];

            if (list.length === 0) {
                content.innerHTML = '<div class="empty">Tidak ada data user</div>';
                return;
            }

            let html = '<table><thead><tr><th>No</th><th>Username</th><th>Nama</th><th>Unit</th><th>Apprver</th><th>Role</th><th>Aksi</th></tr></thead><tbody>';
            list.forEach(r => {
                html += '<tr><td>' + (r.idincrement || '-') + '</td><td>' + (r.username || '-') + '</td><td>' + (r.nama || '-') + '</td><td>' + (r.unit || '-') + '</td><td>' + (r.Apprver ?? '-') + '</td><td>' + (r.role || '-') + '</td><td><button type="button" class="btn btn-sm" onclick="editUser(' + r.idincrement + ')">Edit</button> <button type="button" class="btn btn-sm btn-danger" onclick="delUser(' + r.idincrement + ',\'' + (r.username || '').replace(/'/g, "\\'") + '\')">Hapus</button></td></tr>';
            });
            html += '</tbody></table>';

            const page = meta.page || 1;
            const totalPages = meta.totalPages || 1;
            html += '<div class="pagination"><span>' + (meta.menampilkan || '') + '</span><div><button class="btn btn-sm" onclick="loadUsers(' + (page - 1) + ')" ' + (page <= 1 ? 'disabled' : '') + '>Sebelumnya</button> <button class="btn btn-sm" onclick="loadUsers(' + (page + 1) + ')" ' + (page >= totalPages ? 'disabled' : '') + '>Selanjutnya</button></div></div>';
            content.innerHTML = html;
        }

        function openModal(isEdit = false) {
            document.getElementById('modalTitle').textContent = isEdit ? 'Edit User' : 'Tambah User';
            document.getElementById('editId').value = '';
            document.getElementById('fUsername').value = '';
            document.getElementById('fPassword').value = '';
            document.getElementById('fNama').value = '';
            document.getElementById('fUnit').value = '';
            document.getElementById('fApprver').value = '';
            document.getElementById('fRole').value = '';
            document.getElementById('fUsername').readOnly = false;
            document.getElementById('passRow').style.display = isEdit ? 'block' : 'block';
            document.getElementById('fPassword').required = !isEdit;
            document.getElementById('modalForm').classList.add('show');
        }

        function closeModal() {
            document.getElementById('modalForm').classList.remove('show');
        }

        async function editUser(id) {
            try {
                const res = await fetch(apiBase + '/user-detail', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ token, id })
                });
                const data = await res.json();
                if (data.status == 200 && data.data) {
                    const d = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit User';
                    document.getElementById('editId').value = d.idincrement;
                    document.getElementById('fUsername').value = d.username || '';
                    document.getElementById('fUsername').readOnly = true;
                    document.getElementById('fPassword').value = '';
                    document.getElementById('fPassword').required = false;
                    document.getElementById('passRow').style.display = 'block';
                    document.getElementById('fNama').value = d.nama || '';
                    document.getElementById('fUnit').value = d.unit || '';
                    document.getElementById('fApprver').value = d.Apprver ?? '';
                    document.getElementById('fRole').value = d.role || '';
                    document.getElementById('modalForm').classList.add('show');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal ambil data' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan' });
            }
        }

        async function saveUser() {
            const id = document.getElementById('editId').value;
            const isEdit = !!id;
            const payload = {
                token,
                method: isEdit ? 'userUpdate' : 'userCreate',
                username: document.getElementById('fUsername').value.trim(),
                nama: document.getElementById('fNama').value.trim(),
                unit: document.getElementById('fUnit').value.trim(),
                Apprver: document.getElementById('fApprver').value || null,
                role: document.getElementById('fRole').value.trim()
            };
            if (isEdit) {
                payload.id = parseInt(id, 10);
                const pw = document.getElementById('fPassword').value;
                if (pw) payload.password = pw;
            } else {
                payload.password = document.getElementById('fPassword').value;
            }

            try {
                const url = isEdit ? apiBase + '/user-update' : apiBase + '/user-create';
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.status == 200) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message || 'Tersimpan' });
                    closeModal();
                    loadUsers(1);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menyimpan' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan' });
            }
        }

        async function delUser(id, username) {
            const { isConfirmed } = await Swal.fire({
                icon: 'warning',
                title: 'Hapus User',
                text: 'Yakin hapus user "' + username + '"?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus'
            });
            if (!isConfirmed) return;

            try {
                const res = await fetch(apiBase + '/user-delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ token, id })
                });
                const data = await res.json();
                if (data.status == 200) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'User dihapus' });
                    loadUsers(1);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal hapus' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan' });
            }
        }
    </script>
</body>
</html>
