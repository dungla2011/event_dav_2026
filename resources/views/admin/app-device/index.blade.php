@extends('layouts.adm')

@section('title')
    Mã thiết bị App check-in
@endsection

@section('title_nav_bar')
    MÃ THIẾT BỊ APP CHECK-IN
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <style>
        .appdev-token { font-family: monospace; font-size: 15px; word-break: break-all; user-select: all; }
        .appdev-qr svg { width: 260px; height: 260px; background: #fff; }
        .appdev-off td { color: #999; }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid pt-4" style="max-width: 1100px">

                <h3 class="mb-1">Mã thiết bị App check-in</h3>
                <p class="text-muted">
                    Mỗi máy tính bảng chạy app <b>DAV Event Check-in</b> cần một mã. Trong app vào <b>Cài đặt</b>, bấm
                    <b>Quét QR</b> rồi quét mã QR hiện ra sau khi cấp, hoặc dán mã vào ô <b>Mã thiết bị</b>.
                    Máy bị mất hoặc không dùng nữa thì bấm <b>Thu hồi</b>.
                </p>

                @if (session('app_device_error'))
                    <div class="alert alert-danger">{{ session('app_device_error') }}</div>
                @endif

                @if ($new)
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Đã cấp mã cho thiết bị #{{ $new['id'] }}: {{ $new['name'] }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-auto appdev-qr mb-3">{!! $qr !!}</div>
                                <div class="col">
                                    <p class="mb-1">Mã thiết bị:</p>
                                    <p class="appdev-token mb-2" id="appdev-token">{{ $new['token'] }}</p>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3"
                                            onclick="navigator.clipboard.writeText(document.getElementById('appdev-token').innerText); this.innerText='Đã chép'">
                                        Chép mã
                                    </button>
                                    <div class="alert alert-warning mb-0">
                                        Mã chỉ hiện <b>một lần</b>. Hãy quét QR hoặc chép mã ngay. Tải lại trang là mất,
                                        khi đó phải cấp mã mới.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Cấp mã mới</h3></div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.app-device.store') }}" class="form-inline">
                            @csrf
                            <input type="text" name="name" class="form-control mr-2 mb-2" style="min-width: 320px"
                                   maxlength="128" required placeholder="Tên máy, ví dụ: Kiosk sảnh A">
                            <button type="submit" class="btn btn-primary mb-2">Cấp mã</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Các thiết bị ({{ count($devices) }})</h3></div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                            <tr>
                                <th style="width: 60px">#</th>
                                <th>Tên máy</th>
                                <th>Trạng thái</th>
                                <th>Hoạt động lần cuối</th>
                                <th>Ngày cấp</th>
                                <th style="width: 110px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($devices as $d)
                                <tr class="{{ $d->enabled ? '' : 'appdev-off' }}">
                                    <td>{{ $d->id }}</td>
                                    <td>{{ $d->name }}</td>
                                    <td>
                                        @if ($d->enabled)
                                            <span class="badge badge-success">Đang dùng</span>
                                        @else
                                            <span class="badge badge-secondary">Đã thu hồi</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $d->last_seen_at ? date('d/m/Y H:i', strtotime($d->last_seen_at)) : 'Chưa dùng' }}
                                        @if ($d->last_ip)
                                            <small class="text-muted">({{ $d->last_ip }})</small>
                                        @endif
                                    </td>
                                    <td>{{ $d->created_at ? date('d/m/Y', strtotime($d->created_at)) : '' }}</td>
                                    <td class="text-right">
                                        <form method="post" action="{{ route('admin.app-device.toggle', $d->id) }}"
                                              onsubmit="return confirm('{{ $d->enabled ? 'Thu hồi mã của máy này? App trên máy sẽ không dùng được nữa.' : 'Bật lại mã của máy này?' }}')">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm {{ $d->enabled ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                {{ $d->enabled ? 'Thu hồi' : 'Bật lại' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-muted p-3">Chưa có thiết bị nào.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
