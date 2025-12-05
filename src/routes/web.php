<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::middleware(['auth', 'checkUserAccountIsValid'])
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });

//todo
Route::get('/knowledge', function () { echo 'Knowledge/Index'; })->name('knowledge.index');
Route::get('/folders', function () { echo 'Folders/Index'; })->name('folders.index');
Route::get('/wiki', function () { echo 'Wiki/Index'; })->name('wiki.index');
Route::get('/companies', function () {echo 'Companies/Index';})->name('companies.index');
Route::get('/groups', function () {echo 'Groups/Index';})->name('groups.index');
Route::get('/users', function () {echo 'Users/Index';})->name('users.index');
Route::get('/audit-log', function () {echo 'AuditLog/Index';})->name('audit-log.index');