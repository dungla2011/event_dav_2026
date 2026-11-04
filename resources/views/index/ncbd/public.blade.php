{{-- Trang chủ events.dav.edu.vn (layout ncbd). Trang độc lập, không kế thừa layout chung — phong cách theo event.lad.vn.
     Không có bảng giá, không có hướng dẫn (hướng dẫn chỉ dành cho quản trị viên, xem sau khi đăng nhập).
     Bản gốc trên server: public.blade.php.bak-20261101 --}}
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ trim(strip_tags(\App\Models\SiteMng::getTitle())) ?: 'Hệ thống quản lý sự kiện — Học viện Ngoại giao' }}</title>
<meta name="description" content="Hệ thống quản lý sự kiện của Học viện Ngoại giao: mời khách, xác nhận tham dự và check-in tại cửa bằng mã QR hoặc khuôn mặt.">
<link rel="canonical" href="https://events.dav.edu.vn/">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  :root { --dav:#034671; --dav2:#0a6aa1; --dav-l:#e6f0f7; }
  body { font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",sans-serif; color:#1e293b; }
  .navbar-dav { background:#fff; border-bottom:1px solid #e2e8f0; }
  .navbar-dav .nav-link { color:#334155; } .navbar-dav .nav-link:hover { color:var(--dav); }
  .navbar-dav .brand-logo { height:40px; }
  .navbar-dav .brand-sub { border-left:1px solid #cbd5e1; padding-left:.75rem; margin-left:.75rem; line-height:1.15;
         font-size:.8rem; color:#475569; }
  .navbar-dav .brand-sub b { display:block; color:var(--dav); font-size:.9rem; }
  .btn-dav { background:var(--dav); border-color:var(--dav); color:#fff; } .btn-dav:hover { background:#02375a; color:#fff; }

  .hero { background:linear-gradient(135deg,#021f33 0%,var(--dav) 55%,var(--dav2) 100%); color:#fff; padding:4.5rem 0 4rem; }
  .hero .lead { opacity:.9; }
  .hero .badge-pill { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.25); border-radius:999px;
         padding:.3rem .8rem; font-size:.85rem; display:inline-block; margin:0 .4rem .5rem 0; }
  .hero-cta { display:flex; flex-wrap:wrap; gap:.75rem; }
  .hero-cta .btn { border-radius:12px; padding:.75rem 1.5rem; }
  @media (max-width:575px) { .hero-cta .btn { flex:1 1 100%; } }
  .hero-photo { display:block; width:auto; height:auto; max-width:100%; max-height:600px; margin:0 auto; border-radius:18px;
         box-shadow:0 24px 48px rgba(0,0,0,.35); border:1px solid rgba(255,255,255,.15); }
  @media (max-width:991px) { .hero-photo { max-height:480px; } }
  .banner-dav { display:block; width:100%; max-width:1000px; height:auto; margin:0 auto; border-radius:16px;
         box-shadow:0 12px 32px rgba(15,23,42,.15); }

  .ico { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center;
         background:var(--dav-l); color:var(--dav); font-size:1.35rem; flex-shrink:0; }
  .grp h3 { font-size:1.15rem; font-weight:700; margin-bottom:.9rem; }
  .grp ul { list-style:none; padding:0; margin:0; }
  .grp li { padding:.3rem 0 .3rem 1.6rem; position:relative; color:#475569; }
  .grp li::before { content:"\F26E"; font-family:"bootstrap-icons"; position:absolute; left:0; top:.3rem; color:var(--dav2); }
  .card-grp { border:1px solid #e2e8f0; border-radius:16px; padding:1.5rem; height:100%; background:#fff; }

  /* Khung điện thoại mô phỏng màn hình app check-in */
  .phone { width:220px; max-width:100%; aspect-ratio:9/19; margin:0 auto; border-radius:32px; background:#0f172a;
           padding:10px; box-shadow:0 20px 40px rgba(15,23,42,.25); position:relative; }
  .phone::before { content:""; position:absolute; top:16px; left:50%; transform:translateX(-50%); width:60px; height:6px;
           border-radius:3px; background:#334155; z-index:2; }
  .screen { width:100%; height:100%; border-radius:24px; overflow:hidden; background:#0b1220; color:#e2e8f0; position:relative;
           display:flex; flex-direction:column; font-size:.72rem; }
  .screen img.real { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
  .scr-top { background:var(--dav); color:#fff; padding:28px 12px 10px; font-weight:600; }
  .scr-top small { display:block; font-weight:400; opacity:.85; }
  .scr-body { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:12px; gap:10px; }
  .viewfinder { width:120px; height:120px; position:relative; border-radius:10px; background:#020617; }
  .viewfinder::before { content:""; position:absolute; inset:8px; border:3px solid #22d3ee; border-radius:8px;
           clip-path:polygon(0 0,30% 0,30% 6%,6% 6%,6% 30%,0 30%,0 70%,6% 70%,6% 94%,30% 94%,30% 100%,70% 100%,70% 94%,94% 94%,94% 70%,100% 70%,100% 30%,94% 30%,94% 6%,70% 6%,70% 0,100% 0); }
  .viewfinder i { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:#e2e8f0; font-size:3.4rem; }
  .face { width:120px; height:140px; border-radius:60px/70px; border:3px dashed #22c55e; display:flex; align-items:center;
          justify-content:center; background:#1e293b; color:#94a3b8; font-size:4rem; }
  .pill-ok { background:#dcfce7; color:#166534; border-radius:999px; padding:.25rem .7rem; font-weight:600; }
  .sign { width:150px; height:70px; border-radius:8px; background:#fff; color:#0f172a; display:flex; align-items:center;
          justify-content:center; font-family:"Brush Script MT","Segoe Script",cursive; font-size:1.5rem; }
  .phone-cap { text-align:center; margin-top:1rem; }
  .phone-cap b { display:block; }
  .phone-cap span { color:#64748b; font-size:.9rem; }

  /* Luồng công việc: lưới làn (hàng = vai trò, cột = giai đoạn); màn hẹp đổi thành danh sách theo giai đoạn */
  .flow { display:grid; grid-template-columns:170px repeat(4,1fr); gap:10px; }
  .flow-stage { background:var(--dav); color:#fff; border-radius:10px; padding:.6rem .8rem; font-weight:600; text-align:center; position:relative; }
  .flow-stage i { margin-right:.4rem; }
  .flow-stage:not(:nth-child(5))::after { content:"\F285"; font-family:"bootstrap-icons"; position:absolute; right:-14px; top:50%;
         transform:translateY(-50%); color:var(--dav2); z-index:1; font-size:1rem; }
  .flow-lane { display:flex; align-items:center; gap:.6rem; border-radius:10px; padding:.6rem .8rem; font-size:.95rem; border:0; }
  .flow-lane i { font-size:1.4rem; } .flow-lane small { display:block; color:#64748b; font-size:.78rem; }
  .flow-cell { border-radius:10px; padding:.7rem .8rem; font-size:.9rem; border:1px solid; }
  .flow-cell.empty { border:1px dashed #e2e8f0; background:transparent; }
  .lane-org { background:#e7f1ff; border-color:#b6d4fe; } .flow-lane.lane-org i { color:#0d6efd; }
  .lane-sys { background:#ecfdf5; border-color:#a7f3d0; } .flow-lane.lane-sys i { color:#059669; }
  .lane-guest { background:#fff7ed; border-color:#fed7aa; } .flow-lane.lane-guest i { color:#ea580c; }
  .lane-app { background:#f5f3ff; border-color:#ddd6fe; } .flow-lane.lane-app i { color:#7c3aed; }
  @media (max-width:991px) {
    .flow { grid-template-columns:1fr; gap:6px; }
    .flow-cell { order:var(--o); }
    .flow-corner, .flow-stage, .flow-lane, .flow-cell.empty { display:none; }
    .flow-cell::before { content:attr(data-stage) " · " attr(data-lane); display:block; font-size:.72rem; font-weight:700;
           text-transform:uppercase; letter-spacing:.03em; color:#475569; margin-bottom:.15rem; }
  }
  footer { background:#021f33; color:#9fb3c8; } footer a { color:#cfe0f0; text-decoration:none; }
  .navbar .nav-link, .navbar .btn { white-space:nowrap; }
  @media (max-width:575px) { .navbar-dav .brand-sub { display:none; } }
</style>
</head>
<body>
<nav class="navbar navbar-expand navbar-dav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="/">
      <img class="brand-logo" src="/images/logo/ncbd-event.png" alt="PlanEd">
      <span class="brand-sub"><b>Học viện Ngoại giao</b>Hệ thống quản lý sự kiện</span>
    </a>
    <ul class="navbar-nav ms-auto align-items-center">
      <li class="nav-item d-none d-lg-block"><a class="nav-link" href="#tinh-nang">Tính năng</a></li>
      <li class="nav-item d-none d-lg-block"><a class="nav-link" href="#check-in">Check-in</a></li>
      <li class="nav-item d-none d-lg-block"><a class="nav-link" href="#quy-trinh">Quy trình</a></li>
      @auth
      <li class="nav-item"><a class="btn btn-sm btn-dav ms-2" href="/member">Vào trang quản lý</a></li>
      @else
      <li class="nav-item"><a class="btn btn-sm btn-dav ms-2" href="/login"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a></li>
      @endauth
    </ul>
  </div>
</nav>

<header class="hero">
  <div class="container">
    <div class="row align-items-center g-4 g-lg-5">
      <div class="col-lg-7">
        <div class="text-uppercase small fw-semibold mb-2" style="letter-spacing:.08em;opacity:.8">DAV — Leading Innovation</div>
        <h1 class="display-5 fw-bold mb-3">Quản lý sự kiện chuyên nghiệp, hiệu quả</h1>
        <p class="lead mb-4">Tạo sự kiện, gửi mỗi khách một thư mời có mã QR riêng, nhận xác nhận tham dự và check-in tại cửa bằng mã QR hoặc khuôn mặt.</p>
        <div class="mb-4">
          <span class="badge-pill"><i class="bi bi-envelope me-1"></i>Thư mời Email</span>
          <span class="badge-pill"><i class="bi bi-qr-code me-1"></i>Mã QR</span>
          <span class="badge-pill"><i class="bi bi-person-bounding-box me-1"></i>Nhận diện khuôn mặt</span>
          <span class="badge-pill"><i class="bi bi-pen me-1"></i>Ký xác nhận</span>
        </div>
        <div class="hero-cta">
          @auth
          <a class="btn btn-light btn-lg fw-semibold" href="/member">Vào trang quản lý</a>
          @else
          <a class="btn btn-light btn-lg fw-semibold" href="/login"><i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập</a>
          @endauth
          <a class="btn btn-outline-light btn-lg" href="#check-in"><i class="bi bi-phone me-2"></i>App check-in</a>
        </div>
      </div>
      <div class="col-lg-5">
        <img class="hero-photo" src="/images/event/home/kiosk-photo.jpg" width="485" height="1024"
             alt="Khách mặc âu phục đứng trước máy tính bảng check-in, màn hình nhận diện khuôn mặt tại sảnh sự kiện">
      </div>
    </div>
  </div>
</header>

<section class="py-5" id="tinh-nang">
  <div class="container">
    <h2 class="h3 fw-bold text-center mb-2">Đủ mọi bước của một sự kiện</h2>
    <p class="text-center text-muted mb-5">Từ lúc gửi thư mời tới lúc khách bước qua cửa.</p>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3"><div class="card-grp grp">
        <div class="ico mb-3"><i class="bi bi-calendar2-week"></i></div>
        <h3>Tạo sự kiện</h3>
        <ul>
          <li>Tạo, quản lý và phân loại sự kiện linh hoạt</li>
          <li>Sự kiện chính và các phiên con</li>
          <li>Mô tả có ảnh, song ngữ Việt / Anh</li>
          <li>Đặt giờ mở và đóng check-in</li>
        </ul>
      </div></div>
      <div class="col-md-6 col-lg-3"><div class="card-grp grp">
        <div class="ico mb-3"><i class="bi bi-envelope-paper"></i></div>
        <h3>Mời khách</h3>
        <ul>
          <li>Nhập danh sách từ Excel, phân loại khách mời</li>
          <li>Đăng ký trực tuyến tiện lợi</li>
          <li>Gửi thư mời hàng loạt, mỗi khách một mã QR</li>
          <li>Khách bấm xác nhận hoặc từ chối ngay trong thư</li>
        </ul>
      </div></div>
      <div class="col-md-6 col-lg-3"><div class="card-grp grp">
        <div class="ico mb-3"><i class="bi bi-qr-code-scan"></i></div>
        <h3>Check-in tại cửa</h3>
        <ul>
          <li>Quét QR thư mời trong 1 giây</li>
          <li>Nhận diện khuôn mặt, khách không cần mở thư</li>
          <li>Ký xác nhận trên máy tính bảng</li>
          <li>Hiện ảnh và thông tin khách khi vào</li>
        </ul>
      </div></div>
      <div class="col-md-6 col-lg-3"><div class="card-grp grp">
        <div class="ico mb-3"><i class="bi bi-graph-up"></i></div>
        <h3>Báo cáo</h3>
        <ul>
          <li>Ai đã xác nhận, ai từ chối, ai chưa trả lời</li>
          <li>Danh sách check-in theo từng phiên</li>
          <li>Báo cáo chi tiết và tổng hợp sự kiện</li>
          <li>Xuất dữ liệu để lưu trữ</li>
        </ul>
      </div></div>
    </div>
    <img class="banner-dav mt-5" src="/images/event/dav-home.jpg" width="1400" height="699" loading="lazy"
         alt="Events Management — Học viện Ngoại giao: tạo sự kiện, đăng ký trực tuyến, thông báo, quản lý khách mời, check-in QR, báo cáo">
  </div>
</section>

<section class="py-5 bg-light" id="check-in">
  <div class="container">
    <h2 class="h3 fw-bold text-center mb-2">App check-in trên máy tính bảng</h2>
    <p class="text-center text-muted mb-5">Đặt máy tại cửa — khách đưa mã QR hoặc nhìn vào camera, kết quả về hệ thống ngay.</p>
    <div class="row g-4 g-lg-5 justify-content-center">
      <div class="col-sm-6 col-lg-4">
        <div class="phone"><div class="screen" data-img="app-qr">
          <div class="scr-top">Quét mã QR<small>Hội thảo quốc tế 2026</small></div>
          <div class="scr-body">
            <div class="viewfinder"><i class="bi bi-qr-code"></i></div>
            <div class="text-center" style="color:#94a3b8">Đưa mã QR trên thư mời vào khung</div>
          </div>
        </div></div>
        <div class="phone-cap"><b>1. Quét QR thư mời</b><span>Mỗi khách một mã riêng, không đoán được</span></div>
      </div>
      <div class="col-sm-6 col-lg-4">
        <div class="phone"><div class="screen" data-img="app-ok">
          <div class="scr-top">Nhận diện khuôn mặt<small>Hội thảo quốc tế 2026</small></div>
          <div class="scr-body">
            <div class="face"><i class="bi bi-person"></i></div>
            <span class="pill-ok"><i class="bi bi-person-check me-1"></i>Nguyễn Văn A</span>
          </div>
        </div></div>
        <div class="phone-cap"><b>2. Hoặc nhận diện khuôn mặt</b><span>Khách quên thư mời vẫn vào được</span></div>
      </div>
      <div class="col-sm-6 col-lg-4">
        <div class="phone"><div class="screen" data-img="app-face">
          <div class="scr-top">Ký xác nhận<small>Nguyễn Văn A · Khách mời</small></div>
          <div class="scr-body">
            <div class="sign">Nguyen A</div>
            <span class="pill-ok"><i class="bi bi-check-lg me-1"></i>Check-in thành công</span>
          </div>
        </div></div>
        <div class="phone-cap"><b>3. Ký xác nhận ngay trên máy</b><span>Chữ ký lưu cùng giờ check-in</span></div>
      </div>
    </div>
    <div class="text-center mt-5">
      <span class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-white border small text-muted">
        <i class="bi bi-google-play fs-5" style="color:var(--dav)"></i>
        App “DAV Event Check-in” trên Google Play dành cho thiết bị của ban tổ chức — liên hệ quản trị viên để được cài đặt.
      </span>
    </div>
  </div>
</section>

<section class="py-5" id="quy-trinh">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Luồng công việc</h2>
      <p class="text-muted mb-0">Ai làm gì, ở bước nào — từ lúc chuẩn bị tới sau sự kiện.</p>
    </div>
    @php
      $stages = [['bi-pencil-square', '1. Chuẩn bị'], ['bi-send', '2. Mời & xác nhận'], ['bi-door-open', '3. Ngày sự kiện'], ['bi-bar-chart', '4. Sau sự kiện']];
      $lanes = [
        ['lane-org', 'bi-person-workspace', 'Ban tổ chức', 'trên web', [
          'Tạo sự kiện, các phiên, giờ mở check-in',
          'Nhập danh sách khách, gửi thư mời',
          'Theo dõi trực tiếp ai đã vào, ai chưa đến',
          'Xem báo cáo check-in theo từng phiên']],
        ['lane-sys', 'bi-cloud-check', 'Hệ thống', '', [
          'Sinh cho mỗi khách một link và mã QR riêng',
          'Gửi thư mời, ghi nhận xác nhận tham dự',
          'Đối chiếu QR / khuôn mặt, ghi giờ vào và chữ ký',
          'Lưu lịch sử tham dự']],
        ['lane-guest', 'bi-people', 'Khách mời', '', [
          null,
          'Mở thư mời, bấm xác nhận hoặc từ chối',
          'Đưa mã QR hoặc nhìn vào camera, ký xác nhận',
          null]],
        ['lane-app', 'bi-tablet', 'Máy check-in', 'tại cửa', [
          'Nhập mã thiết bị do quản trị viên cấp',
          null,
          'Quét QR hoặc khuôn mặt — xong trong 1 giây',
          null]],
      ];
    @endphp
    <div class="flow">
      <div class="flow-corner"></div>
      @foreach ($stages as $s)<div class="flow-stage"><i class="bi {{ $s[0] }}"></i>{{ $s[1] }}</div>@endforeach
      @foreach ($lanes as $l)
        <div class="flow-lane {{ $l[0] }}"><i class="bi {{ $l[1] }}"></i><div><b>{{ $l[2] }}</b>@if ($l[3])<small>{{ $l[3] }}</small>@endif</div></div>
        @foreach ($l[4] as $i => $cell)
          @if ($cell)
          <div class="flow-cell {{ $l[0] }}" style="--o:{{ $i + 1 }}" data-stage="{{ $stages[$i][1] }}" data-lane="{{ $l[2] }}">{{ $cell }}</div>
          @else
          <div class="flow-cell empty"></div>
          @endif
        @endforeach
      @endforeach
    </div>
  </div>
</section>

<footer class="py-4">
  <div class="container d-flex flex-wrap justify-content-between gap-2 small">
    <span>© Học viện Ngoại giao · Hệ thống quản lý sự kiện PlanEd</span>
    <span><a href="https://www.dav.edu.vn" target="_blank" rel="noopener">dav.edu.vn</a> · <a href="/privacy-policy">Chính sách bảo mật</a></span>
  </div>
</footer>
<script>
// Ảnh chụp app thật (/images/event/home/<tên>.jpg, chép từ event.lad.vn) đè lên màn mô phỏng; thiếu ảnh thì giữ mô phỏng
document.querySelectorAll('.screen[data-img]').forEach(function (s) {
  var img = new Image();
  img.className = 'real'; img.alt = '';
  img.onload = function () { s.appendChild(img); };
  img.src = '/images/event/home/' + s.dataset.img + '.jpg';
});
</script>
</body>
</html>
