@extends("layouts.adm")

@section("title")
    Quản lý Thành viên sự kiện
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
<style>
    #eum2 .table td, #eum2 .table th { vertical-align: middle; font-size: 14px; }
    #eum2 th.sortable { cursor: pointer; white-space: nowrap; }
    #eum2 th.sortable:hover { background: #eef; }
    #eum2 .filters .form-control, #eum2 .filters .form-select { font-size: 14px; }
    #eum2 .status-yes { color: #198754; font-weight: bold; }
    #eum2 .small-muted { font-size: 12px; color: #6c757d; }
    #eum2 tr.row-checked { background: #fff8e1; }
    #eum2 .table-wrap { overflow-x: auto; }
</style>
@endsection

@section('content')
<div class="content-wrapper" id="eum2">
    <section class="content-header">
        <div class="container-fluid">
            <h4 class="mb-1">Quản lý Thành viên sự kiện</h4>
            <div class="small-muted">Thêm / sửa / xoá thành viên, lọc, và gán thành viên vào sự kiện.</div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- Chọn phạm vi: tất cả thành viên hoặc thành viên của 1 sự kiện --}}
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label mb-0 small">Sự kiện</label>
                            <select id="selEvent" class="form-select form-select-sm">
                                <option value="">— Tất cả thành viên (không lọc theo sự kiện) —</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <button class="btn btn-success btn-sm" id="btnAdd"><i class="fa fa-plus"></i> Thêm thành viên</button>
                            <button class="btn btn-primary btn-sm" id="btnAddToEvent" disabled><i class="fa fa-calendar-plus"></i> Thêm vào sự kiện (<span class="nSel">0</span>)</button>
                            <button class="btn btn-warning btn-sm d-none" id="btnRemoveFromEvent" disabled><i class="fa fa-user-minus"></i> Bỏ khỏi sự kiện (<span class="nSel">0</span>)</button>
                            <button class="btn btn-danger btn-sm" id="btnDeleteSel" disabled><i class="fa fa-trash"></i> Xoá thành viên (<span class="nSel">0</span>)</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bộ lọc --}}
            <div class="card mb-2 filters">
                <div class="card-body py-2">
                    <div class="row g-2" id="filtersAll">
                        <div class="col-6 col-md-2"><input class="form-control form-control-sm" data-sn="s15" placeholder="Họ"></div>
                        <div class="col-6 col-md-2"><input class="form-control form-control-sm" data-sn="s14" placeholder="Tên"></div>
                        <div class="col-6 col-md-2"><input class="form-control form-control-sm" data-sn="s16" placeholder="Email"></div>
                        <div class="col-6 col-md-2"><input class="form-control form-control-sm" data-sn="s17" placeholder="Điện thoại"></div>
                        <div class="col-6 col-md-2"><input class="form-control form-control-sm" data-sn="s19" placeholder="Tổ chức"></div>
                        <div class="col-6 col-md-1">
                            <select class="form-select form-select-sm" data-sn="s21" data-op="eq">
                                <option value="">Ngôn ngữ</option><option value="vi">Việt</option><option value="en">Anh</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-1">
                            <select class="form-select form-select-sm selGroup" data-sn="s10" data-op="eq">
                                <option value="">Nhóm</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 d-none" id="filtersEvent">
                        <div class="col-md-4"><input class="form-control form-control-sm" id="quickSearch" placeholder="Tìm nhanh trong trang: tên, email, nhóm, ghi chú..."></div>
                        <div class="col-4 col-md-2">
                            <select class="form-select form-select-sm" data-sn="s14">
                                <option value="">Xác nhận: tất cả</option><option value="NE">Đã xác nhận</option><option value="E">Chưa xác nhận</option>
                            </select>
                        </div>
                        <div class="col-4 col-md-2">
                            <select class="form-select form-select-sm" data-sn="s15">
                                <option value="">Từ chối: tất cả</option><option value="NE">Đã từ chối</option><option value="E">Chưa từ chối</option>
                            </select>
                        </div>
                        <div class="col-4 col-md-2">
                            <select class="form-select form-select-sm" data-sn="s16">
                                <option value="">Check-in: tất cả</option><option value="NE">Đã check-in</option><option value="E">Chưa check-in</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="small-muted" id="info">Đang tải...</div>
                        <div>
                            <select id="selLimit" class="form-select form-select-sm d-inline-block" style="width:auto">
                                <option>20</option><option selected>50</option><option>100</option><option>200</option><option>500</option>
                            </select>
                            <button class="btn btn-outline-secondary btn-sm" id="btnClear">Xoá lọc</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bảng --}}
            <div class="card">
                <div class="card-body p-0 table-wrap">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-light" id="thead"></thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
                <div class="card-footer py-2"><ul class="pagination pagination-sm mb-0 flex-wrap" id="pager"></ul></div>
            </div>
        </div>
    </section>
</div>

{{-- Modal thêm / sửa thành viên --}}
<div class="modal fade" id="mdlUser" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="frmUser" autocomplete="off">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="mdlUserTitle">Thêm thành viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id">
                <div class="row g-2">
                    <div class="col-md-2"><label class="form-label small mb-0">Danh xưng</label><input class="form-control form-control-sm" name="title" placeholder="Ông/Bà/Mr/Ms"></div>
                    <div class="col-md-5"><label class="form-label small mb-0">Họ</label><input class="form-control form-control-sm" name="last_name"></div>
                    <div class="col-md-5"><label class="form-label small mb-0">Tên <span class="text-danger">*</span></label><input class="form-control form-control-sm" name="first_name" required></div>
                    <div class="col-md-6"><label class="form-label small mb-0">Email <span class="text-danger">*</span></label><input class="form-control form-control-sm" name="email" type="email" required></div>
                    <div class="col-md-6"><label class="form-label small mb-0">Điện thoại</label><input class="form-control form-control-sm" name="phone"></div>
                    <div class="col-md-6"><label class="form-label small mb-0">Tổ chức</label><input class="form-control form-control-sm" name="organization"></div>
                    <div class="col-md-6"><label class="form-label small mb-0">Chức vụ</label><input class="form-control form-control-sm" name="designation"></div>
                    <div class="col-md-4"><label class="form-label small mb-0">Ngôn ngữ</label>
                        <select class="form-select form-select-sm" name="language"><option value="vi">Việt</option><option value="en">Anh</option></select></div>
                    <div class="col-md-8"><label class="form-label small mb-0">Nhóm chính</label>
                        <select class="form-select form-select-sm selGroup" name="parent_id"><option value="">— Không —</option></select></div>
                    <div class="col-12"><label class="form-label small mb-0">Ghi chú</label><textarea class="form-control form-control-sm" name="note" rows="2"></textarea></div>
                    <div class="col-12 d-none" id="wrapAddToCurEvent">
                        <label class="form-check-label small"><input type="checkbox" class="form-check-input" id="chkAddToCurEvent" checked>
                            Thêm luôn vào sự kiện đang chọn: <b id="curEventName"></b></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Huỷ</button>
                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal thêm thành viên đã chọn vào sự kiện --}}
<div class="modal fade" id="mdlToEvent" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">Thêm <span class="nSel">0</span> thành viên vào sự kiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input class="form-control form-control-sm mb-2" id="evSearch" placeholder="Lọc tên sự kiện...">
                <select id="selEventsTarget" class="form-select form-select-sm" multiple size="12"></select>
                <div class="small-muted mt-1">Giữ Ctrl để chọn nhiều sự kiện. Thành viên đã có trong sự kiện sẽ được bỏ qua.</div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Huỷ</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnDoAddToEvent">Thêm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function () {
    // ===== Cấu hình: chỉ gọi các API có sẵn =====
    const API = {
        userList:   '/api/event-user-info/list',
        userGet:    id => '/api/event-user-info/get/' + id,
        userAdd:    '/api/event-user-info/add',
        userUpdate: id => '/api/event-user-info/update/' + id,
        userDelete: '/api/event-user-info/delete',
        eauList:    '/api/event-and-user/list',
        eauDelete:  '/api/event-and-user/delete',
        eventList:  '/api/event-info/list',
        groupList:  '/api/event-user-group/list',
        addToEvent: '/api/event-info/addUserToEvent',
    };
    const TOKEN = (window.jctool && jctool.getCookie('_tglx863516839')) || '';

    const state = { page: 1, sort: { sn: 's1', dir: 'desc' }, rows: [], selected: new Map(), events: [], groups: {} };
    const $ = s => document.querySelector(s);
    const $$ = s => Array.from(document.querySelectorAll(s));
    const esc = v => (v === null || v === undefined) ? '' : String(v).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const curEventId = () => $('#selEvent').value;
    const isEventMode = () => !!curEventId();

    async function api(method, url, data) {
        const opt = { method, headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' } };
        if (method === 'GET' && data) url += (url.includes('?') ? '&' : '?') + new URLSearchParams(data).toString();
        if (method !== 'GET') { opt.headers['Content-Type'] = 'application/json'; opt.body = JSON.stringify(data || {}); }
        const res = await fetch(url, opt);
        let js = null;
        try { js = await res.json(); } catch (e) { throw new Error('HTTP ' + res.status + ' (không phải JSON)'); }
        if (!res.ok || js.code < 0) throw new Error((js && (js.message || js.payload)) || ('HTTP ' + res.status));
        return js.payload;
    }
    const ok = m => (window.toastr ? toastr.success(m) : alert(m));
    const fail = e => (window.toastr ? toastr.error(String(e.message || e)) : alert(e.message || e));

    // ===== Dữ liệu phụ: sự kiện, nhóm =====
    async function loadEvents() {
        const p = await api('GET', API.eventList, { limit: 1000, soby_s1: 'desc' });
        state.events = (p.data || []).map(e => ({ id: e.id, name: e.name || ('#' + e.id) }));
        $('#selEvent').insertAdjacentHTML('beforeend', state.events.map(e => `<option value="${esc(e.id)}">#${esc(e.id)} — ${esc(e.name)}</option>`).join(''));
        renderEventTargets();
        const fromUrl = new URLSearchParams(location.search).get('event_id');
        if (fromUrl) $('#selEvent').value = fromUrl;
    }
    async function loadGroups() {
        const p = await api('GET', API.groupList, { limit: 1000 });
        const list = (p.data || []).map(g => ({ id: g.id, name: g.name || ('#' + g.id) })).sort((a, b) => a.name.localeCompare(b.name, 'vi'));
        list.forEach(g => state.groups[g.id] = g.name);
        $$('.selGroup').forEach(sel => sel.insertAdjacentHTML('beforeend', list.map(g => `<option value="${esc(g.id)}">${esc(g.name)}</option>`).join('')));
    }
    function renderEventTargets() {
        const q = ($('#evSearch').value || '').toLowerCase();
        $('#selEventsTarget').innerHTML = state.events.filter(e => !q || e.name.toLowerCase().includes(q) || String(e.id) === q)
            .map(e => `<option value="${esc(e.id)}">#${esc(e.id)} — ${esc(e.name)}</option>`).join('');
    }

    // ===== Danh sách =====
    const COLS_ALL = [
        { sn: 's1',  label: 'ID' },
        { sn: 's15', label: 'Họ tên' },
        { sn: 's16', label: 'Email' },
        { sn: 's17', label: 'Điện thoại' },
        { sn: 's19', label: 'Tổ chức' },
        { sn: null,  label: 'Chức vụ' },
        { sn: 's10', label: 'Nhóm' },
        { sn: 's21', label: 'NN' },
    ];
    const COLS_EV = [
        { sn: 's4',  label: 'ID khách' },
        { sn: null,  label: 'Họ tên' },
        { sn: null,  label: 'Email' },
        { sn: null,  label: 'Nhóm' },
        { sn: 's14', label: 'Xác nhận' },
        { sn: 's15', label: 'Từ chối' },
        { sn: 's16', label: 'Check-in' },
        { sn: 's17', label: 'Ghi chú' },
    ];

    function renderHead() {
        const cols = isEventMode() ? COLS_EV : COLS_ALL;
        $('#thead').innerHTML = '<tr><th style="width:30px"><input type="checkbox" id="chkAll"></th>' +
            cols.map(c => c.sn
                ? `<th class="sortable" data-sn="${c.sn}">${c.label} ${state.sort.sn === c.sn ? (state.sort.dir === 'asc' ? '▲' : '▼') : ''}</th>`
                : `<th>${c.label}</th>`).join('') +
            '<th style="width:1%;white-space:nowrap">Thao tác</th></tr>';
        $('#chkAll').onchange = e => { $$('#tbody .chkRow').forEach(c => { if (c.closest('tr').style.display !== 'none') { c.checked = e.target.checked; toggleSel(c); } }); };
        $$('#thead th.sortable').forEach(th => th.onclick = () => {
            const sn = th.dataset.sn;
            state.sort = { sn, dir: state.sort.sn === sn && state.sort.dir === 'desc' ? 'asc' : 'desc' };
            state.page = 1; load();
        });
    }

    function buildParams() {
        const p = { page: state.page, limit: $('#selLimit').value };
        p['soby_' + state.sort.sn] = state.sort.dir;
        if (isEventMode()) {
            p.seby_s5 = curEventId();
            $$('#filtersEvent select[data-sn]').forEach(s => { if (s.value) { p['seby_' + s.dataset.sn] = '1'; p['seoby_' + s.dataset.sn] = s.value; } });
        } else {
            $$('#filtersAll [data-sn]').forEach(el => {
                const v = el.value.trim(); if (!v) return;
                p['seby_' + el.dataset.sn] = v; p['seoby_' + el.dataset.sn] = el.dataset.op || 'C';
            });
        }
        return p;
    }

    let loadSeq = 0;
    async function load() {
        const seq = ++loadSeq;
        $('#info').textContent = 'Đang tải...';
        renderHead();
        try {
            const p = await api('GET', isEventMode() ? API.eauList : API.userList, buildParams());
            if (seq !== loadSeq) return;
            state.rows = p.data || [];
            renderRows();
            renderPager(p);
            $('#info').textContent = `Tổng ${p.total} ${isEventMode() ? 'thành viên trong sự kiện' : 'thành viên'} · trang ${p.current_page}/${p.last_page}`;
        } catch (e) {
            $('#tbody').innerHTML = `<tr><td colspan="20" class="text-danger p-3">Lỗi tải dữ liệu: ${esc(e.message)}</td></tr>`;
            $('#info').textContent = '';
        }
    }

    const yes = v => v ? `<span class="status-yes" title="${esc(v)}">✔</span>` : '';
    function renderRows() {
        const ev = isEventMode();
        if (!state.rows.length) { $('#tbody').innerHTML = '<tr><td colspan="20" class="text-center p-3 small-muted">Không có dữ liệu</td></tr>'; return; }
        $('#tbody').innerHTML = state.rows.map(r => {
            // key chọn: user id (để thêm vào sự kiện / xoá thành viên); eau id để bỏ khỏi sự kiện
            const uid = ev ? r.user_event_id : r.id;
            const checked = state.selected.has(String(uid));
            if (ev) {
                return `<tr data-uid="${esc(uid)}" data-eau="${esc(r.id)}" class="${checked ? 'row-checked' : ''}">
                    <td><input type="checkbox" class="chkRow" ${checked ? 'checked' : ''}></td>
                    <td>${esc(uid)}</td>
                    <td class="qs">${esc(r._last_name)} ${esc(r._first_name)}</td>
                    <td class="qs">${esc(r._email)}</td>
                    <td class="qs">${esc(r._group_name)}</td>
                    <td class="text-center">${yes(r.confirm_join_at)}</td>
                    <td class="text-center">${yes(r.deny_join_at)}</td>
                    <td class="text-center">${yes(r.attend_at)}</td>
                    <td class="qs">${esc(r.note)}</td>
                    <td class="text-nowrap">
                        <button class="btn btn-outline-primary btn-xs btn-sm py-0 act-edit" title="Sửa thông tin thành viên"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-outline-warning btn-sm py-0 act-remove" title="Bỏ khỏi sự kiện"><i class="fa fa-user-minus"></i></button>
                    </td></tr>`;
            }
            return `<tr data-uid="${esc(uid)}" class="${checked ? 'row-checked' : ''}">
                <td><input type="checkbox" class="chkRow" ${checked ? 'checked' : ''}></td>
                <td>${esc(r.id)}</td>
                <td>${esc(r.title)} ${esc(r.last_name)} ${esc(r.first_name)}</td>
                <td>${esc(r.email)}</td>
                <td>${esc(r.phone)}</td>
                <td>${esc(r.organization)}</td>
                <td>${esc(r.designation)}</td>
                <td>${esc(state.groups[r.parent_id] || '')}</td>
                <td>${esc(r.language)}</td>
                <td class="text-nowrap">
                    <button class="btn btn-outline-primary btn-sm py-0 act-edit" title="Sửa"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm py-0 act-del" title="Xoá"><i class="fa fa-trash"></i></button>
                </td></tr>`;
        }).join('');
        $$('#tbody .chkRow').forEach(c => c.onchange = () => toggleSel(c));
        $$('#tbody .act-edit').forEach(b => b.onclick = () => openEdit(b.closest('tr').dataset.uid));
        $$('#tbody .act-del').forEach(b => b.onclick = () => deleteUsers([b.closest('tr').dataset.uid]));
        $$('#tbody .act-remove').forEach(b => b.onclick = () => removeFromEvent([b.closest('tr').dataset.eau]));
        applyQuickSearch();
    }

    function renderPager(p) {
        const cur = p.current_page, last = p.last_page;
        if (last <= 1) { $('#pager').innerHTML = ''; return; }
        const pages = new Set([1, last]);
        for (let i = cur - 3; i <= cur + 3; i++) if (i >= 1 && i <= last) pages.add(i);
        let prev = 0, html = '';
        [...pages].sort((a, b) => a - b).forEach(n => {
            if (n - prev > 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
            html += `<li class="page-item ${n === cur ? 'active' : ''}"><a class="page-link" href="#" data-p="${n}">${n}</a></li>`;
            prev = n;
        });
        $('#pager').innerHTML = html;
        $$('#pager a').forEach(a => a.onclick = e => { e.preventDefault(); state.page = +a.dataset.p; load(); });
    }

    function applyQuickSearch() {
        if (!isEventMode()) return;
        const q = $('#quickSearch').value.trim().toLowerCase();
        $$('#tbody tr[data-uid]').forEach(tr => {
            const txt = Array.from(tr.querySelectorAll('.qs')).map(td => td.textContent).join(' ').toLowerCase();
            tr.style.display = !q || txt.includes(q) ? '' : 'none';
        });
    }

    // ===== Chọn nhiều =====
    function toggleSel(chk) {
        const tr = chk.closest('tr');
        if (chk.checked) state.selected.set(tr.dataset.uid, tr.dataset.eau || null); else state.selected.delete(tr.dataset.uid);
        tr.classList.toggle('row-checked', chk.checked);
        updateSelUi();
    }
    function updateSelUi() {
        const n = state.selected.size;
        $$('.nSel').forEach(s => s.textContent = n);
        $('#btnAddToEvent').disabled = !n;
        $('#btnDeleteSel').disabled = !n || isEventMode();
        $('#btnRemoveFromEvent').disabled = !n;
        $('#btnRemoveFromEvent').classList.toggle('d-none', !isEventMode());
        $('#btnDeleteSel').classList.toggle('d-none', isEventMode());
    }
    function clearSel() { state.selected.clear(); updateSelUi(); }

    // ===== Thêm / sửa =====
    const mdlUser = new bootstrap.Modal($('#mdlUser'));
    const frm = $('#frmUser');
    function openAdd() {
        frm.reset(); frm.id.value = '';
        $('#mdlUserTitle').textContent = 'Thêm thành viên';
        $('#wrapAddToCurEvent').classList.toggle('d-none', !isEventMode());
        $('#curEventName').textContent = $('#selEvent').selectedOptions[0]?.textContent || '';
        mdlUser.show();
    }
    async function openEdit(uid) {
        try {
            const u = await api('GET', API.userGet(uid));
            frm.reset();
            ['id', 'title', 'last_name', 'first_name', 'email', 'phone', 'organization', 'designation', 'language', 'parent_id', 'note']
                .forEach(f => { if (frm[f]) frm[f].value = u[f] ?? ''; });
            frm.id.value = uid;
            $('#mdlUserTitle').textContent = 'Sửa thành viên #' + uid;
            $('#wrapAddToCurEvent').classList.add('d-none');
            mdlUser.show();
        } catch (e) { fail(e); }
    }
    frm.onsubmit = async e => {
        e.preventDefault();
        const data = {};
        ['title', 'last_name', 'first_name', 'email', 'phone', 'organization', 'designation', 'language', 'parent_id', 'note']
            .forEach(f => data[f] = frm[f].value.trim());
        try {
            if (frm.id.value) {
                await api('POST', API.userUpdate(frm.id.value), data);
                ok('Đã cập nhật thành viên #' + frm.id.value);
            } else {
                const newId = await api('POST', API.userAdd, data);
                ok('Đã thêm thành viên #' + newId);
                if (isEventMode() && $('#chkAddToCurEvent').checked) {
                    ok(await api('POST', API.addToEvent, { mmSelectId: [newId], mmEventCheck: [curEventId()] }));
                }
            }
            mdlUser.hide(); load();
        } catch (err) { fail(err); }
    };

    // ===== Xoá / bỏ khỏi sự kiện / thêm vào sự kiện =====
    async function deleteUsers(ids) {
        if (!ids.length || !confirm(`Xoá ${ids.length} thành viên?\n\nThành viên bị xoá sẽ không còn trong danh bạ (vào thùng rác, có thể khôi phục).`)) return;
        try { await api('GET', API.userDelete, { id: ids.join(',') }); ok('Đã xoá ' + ids.length + ' thành viên'); clearSel(); load(); }
        catch (e) { fail(e); }
    }
    async function removeFromEvent(eauIds) {
        if (!eauIds.length || !confirm(`Bỏ ${eauIds.length} thành viên khỏi sự kiện này?\n\n(Chỉ bỏ liên kết với sự kiện, không xoá thành viên.)`)) return;
        try { await api('GET', API.eauDelete, { id: eauIds.join(',') }); ok('Đã bỏ ' + eauIds.length + ' thành viên khỏi sự kiện'); clearSel(); load(); }
        catch (e) { fail(e); }
    }
    const mdlToEvent = new bootstrap.Modal($('#mdlToEvent'));
    $('#btnDoAddToEvent').onclick = async () => {
        const evs = Array.from($('#selEventsTarget').selectedOptions).map(o => o.value);
        if (!evs.length) return alert('Hãy chọn ít nhất 1 sự kiện');
        try {
            ok(await api('POST', API.addToEvent, { mmSelectId: [...state.selected.keys()], mmEventCheck: evs }));
            mdlToEvent.hide(); clearSel(); load();
        } catch (e) { fail(e); }
    };

    // ===== Sự kiện UI =====
    $('#btnAdd').onclick = openAdd;
    $('#btnAddToEvent').onclick = () => { $('#evSearch').value = ''; renderEventTargets(); mdlToEvent.show(); };
    $('#btnDeleteSel').onclick = () => deleteUsers([...state.selected.keys()]);
    $('#btnRemoveFromEvent').onclick = () => removeFromEvent([...state.selected.values()].filter(Boolean));
    $('#evSearch').oninput = renderEventTargets;
    $('#quickSearch').oninput = applyQuickSearch;
    $('#selLimit').onchange = () => { state.page = 1; load(); };
    $('#selEvent').onchange = () => {
        const ev = isEventMode();
        $('#filtersAll').classList.toggle('d-none', ev);
        $('#filtersEvent').classList.toggle('d-none', !ev);
        state.sort = { sn: ev ? 's4' : 's1', dir: 'desc' };
        state.page = 1; clearSel();
        const u = new URL(location.href);
        ev ? u.searchParams.set('event_id', curEventId()) : u.searchParams.delete('event_id');
        history.replaceState(null, '', u);
        load();
    };
    let tmr;
    $$('#filtersAll [data-sn], #filtersEvent select[data-sn]').forEach(el =>
        el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', () => { clearTimeout(tmr); tmr = setTimeout(() => { state.page = 1; load(); }, 400); }));
    $('#btnClear').onclick = () => {
        $$('#filtersAll [data-sn], #filtersEvent [data-sn]').forEach(el => el.value = '');
        $('#quickSearch').value = ''; state.page = 1; load();
    };

    // ===== Khởi động =====
    (async () => {
        try { await Promise.all([loadEvents(), loadGroups()]); } catch (e) { fail(e); }
        $('#selEvent').dispatchEvent(new Event('change'));
    })();
})();
</script>
@endsection
