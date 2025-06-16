<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

// 重定向根路径到登录页面
Route::get('/', function () {
    return redirect()->route('login');
});

// 登录相关路由
Route::get('/login', [VideoController::class, 'showLogin'])->name('login');
Route::post('/login', [VideoController::class, 'login'])->name('login.post');
Route::post('/logout', [VideoController::class, 'logout'])->name('logout');

// 视频相关路由
Route::get('/videos', [VideoController::class, 'videoList'])->name('video.list');
Route::get('/videos/load-more', [VideoController::class, 'loadMoreVideos'])->name('video.load-more');
Route::get('/video/{id}', [VideoController::class, 'videoDetail'])->name('video.detail');
