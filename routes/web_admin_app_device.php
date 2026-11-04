<?php

use App\Components\Route2;

// Mã thiết bị cho app kiosk check-in. Không có dòng permission nào cho các route này,
// nên chỉ user is_admin qua được (Gate::before trong AuthServiceProvider).
Route2::prefix('admin')->group(function () {

    Route2::prefix('app-device')->group(function () {
        $route_group_desc = 'Mã thiết bị App check-in';

        $routeName = 'admin.app-device.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\AppDeviceAdminController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Danh sách mã thiết bị';

        $routeName = 'admin.app-device.store';
        $r = Route2::post('/', [
            \App\Http\Controllers\AppDeviceAdminController::class, 'store',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Cấp mã thiết bị';

        $routeName = 'admin.app-device.toggle';
        $r = Route2::post('/toggle/{id}', [
            \App\Http\Controllers\AppDeviceAdminController::class, 'toggle',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Thu hồi / bật lại mã thiết bị';
    });

});
