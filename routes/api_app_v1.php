<?php

// API app kiosk check-in (Event-DavAppFace2026). File được RouteServiceProvider nạp trong nhóm prefix "api".
// Dùng Route thường (không Route2) để không đi qua hệ phân quyền "can:" của trang quản trị.

use App\Http\ControllerApi\AppV1Controller;
use App\Http\Middleware\AppV1DeviceAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('app/v1')
    ->middleware(AppV1DeviceAuth::class)
    ->group(function () {
        Route::get('/me', [AppV1Controller::class, 'me'])->name('api.app_v1.me');
        Route::post('/events/{eventId}/face', [AppV1Controller::class, 'face'])->name('api.app_v1.face');
        Route::post('/events/{eventId}/qr', [AppV1Controller::class, 'qr'])->name('api.app_v1.qr');
        Route::post('/events/{eventId}/checkin', [AppV1Controller::class, 'checkin'])->name('api.app_v1.checkin');
    });
