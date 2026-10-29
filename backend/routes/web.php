<?php

use Illuminate\Support\Facades\Route;

// 前端 SPA 頁面路由：未匹配到 API 的路徑均由前端 index.html 處理
Route::get('/{any}', function () {
    $indexPath = public_path('index.html');
    if (file_exists($indexPath)) {
        return response()->file($indexPath);
    }
    return view('welcome');
})->where('any', '^(?!api|sanctum).*$');
