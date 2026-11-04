<?php

use App\Components\Route2;

// Trợ lý AI (widget góc phải layouts.adm, chỉ hiện ở các trang /admin).
// Mọi user đã đăng nhập đều hỏi được, nên dùng 'auth' thay cho 'can:'
// (route không có dòng permission → 'can:' chỉ cho admin qua).
Route2::prefix('admin')->group(function () {
    $route_group_desc = 'Trợ lý AI';

    $r = Route2::post('/guide-chat', [
        \App\Http\Controllers\GuideChatController::class, 'chat',
    ])->name('admin.guide-chat');
    $r->middleware('auth');
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Hỏi trợ lý AI hướng dẫn sử dụng';

    $r = Route2::get('/guide-chat/history', [
        \App\Http\Controllers\GuideChatController::class, 'history',
    ])->name('admin.guide-chat.history');
    $r->middleware('auth');
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lịch sử chat với trợ lý AI của user đang đăng nhập';
});
