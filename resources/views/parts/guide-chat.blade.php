{{-- Trợ lý AI hướng dẫn sử dụng (giống trợ lý của mytree.vn). Gọi POST /admin/guide-chat → GuideChatController.
     Lịch sử chat lưu trên server theo user đăng nhập (GET /admin/guide-chat/history khi mở widget). --}}
@auth
<style>
    #gc-fab{position:fixed;right:20px;bottom:20px;z-index:1040;width:56px;height:56px;border-radius:50%;border:0;
        background:#0b6efd;color:#fff;font-size:24px;box-shadow:0 4px 14px rgba(0,0,0,.25);cursor:pointer}
    #gc-box{position:fixed;right:20px;bottom:88px;z-index:1041;width:380px;max-width:calc(100vw - 32px);height:540px;
        max-height:calc(100vh - 110px);background:#fff;border-radius:14px;box-shadow:0 8px 30px rgba(0,0,0,.25);
        display:none;flex-direction:column;overflow:hidden;font-size:14px}
    #gc-box.gc-open{display:flex}
    #gc-box.gc-big{width:760px;height:calc(100vh - 110px)}
    #gc-head{background:#0b6efd;color:#fff;padding:10px 12px;display:flex;align-items:center;gap:8px}
    #gc-head b{font-size:16px}
    #gc-head small{display:block;opacity:.85;font-size:12px}
    #gc-head button{background:none;border:0;color:#fff;font-size:16px;cursor:pointer;padding:4px 6px}
    #gc-msgs{flex:1;overflow-y:auto;padding:12px;background:#fff}
    .gc-m{max-width:85%;margin-bottom:10px;padding:8px 12px;border-radius:14px;line-height:1.45;word-wrap:break-word}
    .gc-m ul,.gc-m ol{margin:4px 0;padding-left:20px}
    .gc-m p{margin:0 0 6px}.gc-m p:last-child{margin:0}
    .gc-a{background:#f0f1f5;color:#222}
    .gc-u{background:#0b6efd;color:#fff;margin-left:auto;white-space:pre-wrap}
    .gc-u a{color:#fff}
    .gc-t{font-size:11px;color:#999;margin:-6px 4px 10px}
    .gc-t.gc-r{text-align:right}
    #gc-foot{border-top:1px solid #eee;padding:8px;display:flex;gap:8px;align-items:flex-end}
    #gc-in{flex:1;resize:none;border:1px solid #ccd;border-radius:18px;padding:8px 14px;max-height:120px;outline:none}
    #gc-send{width:40px;height:40px;border-radius:50%;border:0;background:#0b6efd;color:#fff;flex:none}
    #gc-send:disabled{background:#c8ccd4}
    #gc-clear{font-size:12px;color:#888;text-align:center;padding:4px;border-top:1px solid #f3f3f3;background:#fafafa}
    #gc-clear a{color:#0b6efd;cursor:pointer}
</style>

<button id="gc-fab" type="button" title="Trợ lý AI"><i class="fas fa-robot"></i></button>
<div id="gc-box" role="dialog" aria-label="Trợ lý AI">
    <div id="gc-head">
        <div style="flex:1;min-width:0"><b>Trợ lý AI</b><small>Hỏi đáp cách dùng trang quản trị &amp; app check-in</small></div>
        <button type="button" id="gc-big" title="Phóng to / thu nhỏ"><i class="fas fa-expand"></i></button>
        <button type="button" id="gc-x" title="Đóng"><i class="fas fa-times"></i></button>
    </div>
    <div id="gc-msgs"></div>
    <div id="gc-clear">Trả lời do AI tạo, có thể chưa chính xác · <a id="gc-reset">Cuộc trò chuyện mới</a></div>
    <div id="gc-foot">
        <textarea id="gc-in" rows="1" placeholder="Nhập câu hỏi..."></textarea>
        <button type="button" id="gc-send" title="Gửi" disabled><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
(function () {
    var URL = @json(url('/admin/guide-chat'));
    var HELLO = 'Chào bạn! Mình có thể hướng dẫn cách dùng trang quản trị sự kiện và app **DAV Event Check-in**: cài app, cấp mã thiết bị, check-in bằng khuôn mặt / QR, xử lý lỗi thường gặp... Bạn cần giúp gì?';

    var box = document.getElementById('gc-box'), msgs = document.getElementById('gc-msgs'),
        input = document.getElementById('gc-in'), send = document.getElementById('gc-send');
    var history = [], convId = null, busy = false, loaded = false;

    function call(method, url, body) {
        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content;
        return fetch(url, {
            method: method, credentials: 'same-origin',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token},
            body: body ? JSON.stringify(body) : undefined
        }).then(function (r) {
            return r.json().catch(function () { return {}; }).then(function (j) {
                if (r.status === 419 || r.status === 401) throw new Error('Phiên đăng nhập đã hết hạn, hãy tải lại trang.');
                if (!r.ok) throw new Error(j.error || 'Không gửi được câu hỏi, vui lòng thử lại.');
                return j;
            });
        });
    }
    // Mở widget lần đầu: lấy cuộc trò chuyện gần nhất của user từ server.
    function load() {
        if (loaded) return;
        loaded = true;
        call('GET', URL + '/history').then(function (j) {
            if (busy || history.length) return;   // đã hỏi trong lúc đang tải
            convId = j.conversation_id; history = j.messages || []; render();
        }, function () { loaded = false; });
    }

    function esc(s) { return s.replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
    function inline(s) {
        return s.replace(/\*\*(.+?)\*\*/g, '<b>$1</b>')
            .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>')
            .replace(/(^|[\s(])(https?:\/\/[^\s<)]+)/g, '$1<a href="$2" target="_blank" rel="noopener">$2</a>');
    }
    // Markdown tối giản: đoạn văn, danh sách "- " / "1. ", in đậm, liên kết. Escape trước nên không chèn được HTML.
    function md(text) {
        var out = [], list = null;
        esc(text).split('\n').forEach(function (line) {
            var m = line.match(/^\s*(?:[-*•]|(\d+)[.)])\s+(.*)$/);
            if (m) {
                var tag = m[1] ? 'ol' : 'ul';
                if (list !== tag) { if (list) out.push('</' + list + '>'); out.push('<' + tag + '>'); list = tag; }
                out.push('<li>' + inline(m[2]) + '</li>');
                return;
            }
            if (list) { out.push('</' + list + '>'); list = null; }
            if (line.trim()) out.push('<p>' + inline(line.replace(/^#+\s*/, '')) + '</p>');
        });
        if (list) out.push('</' + list + '>');
        return out.join('');
    }
    function hhmm(ts) { var d = new Date(ts); return ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2); }

    function bubble(m) {
        var div = document.createElement('div');
        div.className = 'gc-m ' + (m.role === 'user' ? 'gc-u' : 'gc-a');
        if (m.role === 'user') div.textContent = m.content; else div.innerHTML = md(m.content);
        msgs.appendChild(div);
        if (m.ts) {
            var t = document.createElement('div');
            t.className = 'gc-t' + (m.role === 'user' ? ' gc-r' : '');
            t.textContent = hhmm(m.ts);
            msgs.appendChild(t);
        }
        return div;
    }
    function render() {
        msgs.innerHTML = '';
        bubble({role: 'assistant', content: HELLO});
        history.forEach(bubble);
        msgs.scrollTop = msgs.scrollHeight;
    }
    function sync() { send.disabled = busy || !input.value.trim(); }

    function ask() {
        var q = input.value.trim();
        if (!q || busy) return;
        busy = true; input.value = ''; input.style.height = ''; sync();
        history.push({role: 'user', content: q, ts: Date.now()});
        bubble(history[history.length - 1]);
        var wait = bubble({role: 'assistant', content: '...'});
        msgs.scrollTop = msgs.scrollHeight;

        call('POST', URL, {message: q, conversation_id: convId}).then(function (j) {
            convId = j.conversation_id;
            history.push({role: 'assistant', content: j.reply, ts: Date.now()});
            render();
        }, function (e) {
            wait.innerHTML = '<i style="color:#c00">' + esc(e.message) + '</i>';
            history.pop();   // server không lưu câu hỏi lỗi, bỏ khỏi màn hình ở lượt vẽ lại sau
            input.value = q; sync();
        }).then(function () { busy = false; sync(); input.focus(); });
    }

    document.getElementById('gc-fab').onclick = function () {
        var open = box.classList.toggle('gc-open');
        if (open) { render(); load(); input.focus(); }
    };
    document.getElementById('gc-x').onclick = function () { box.classList.remove('gc-open'); };
    document.getElementById('gc-big').onclick = function () { box.classList.toggle('gc-big'); msgs.scrollTop = msgs.scrollHeight; };
    // Cuộc trò chuyện mới: câu hỏi kế tiếp tạo conversation mới trên server; cuộc cũ vẫn lưu trong DB.
    document.getElementById('gc-reset').onclick = function () { if (busy) return; history = []; convId = null; loaded = true; render(); };
    send.onclick = ask;
    input.addEventListener('input', function () {
        sync(); input.style.height = ''; input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); ask(); }
    });
})();
</script>
@endauth
