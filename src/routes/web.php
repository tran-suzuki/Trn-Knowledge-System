<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\User\UserController;
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

Route::middleware(['auth'])->group(function () {
	Route::get('/users', [UserController::class, 'index'])->name('user.index');
	Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
	Route::post('/users', [UserController::class, 'store'])->name('user.store');
	Route::get('/users/{user:display_id}/edit', [UserController::class, 'edit'])->name('user.edit');
	Route::put('/users/{user:display_id}', [UserController::class, 'update'])->name('user.update');
	Route::delete('/users/{user:display_id}', [UserController::class, 'destroy'])->name('user.destroy');
	Route::post('/users/{user:display_id}/check-lock', [UserController::class, 'checkLock'])->name('user.checkLock');
	Route::post('/users/{user:display_id}/check-password', [UserController::class, 'checkPassword'])->name('user.checkPassword');
	Route::get('/users/email-verify-change', [UserController::class, 'confirmEmailChange'])->name('user.email-verify-change');
});

//todo
Route::get('/knowledge', function () {echo 'Knowledge/Index';})->name('knowledge.index');
Route::get('/folders', function () {echo 'Folders/Index';})->name('folders.index');
Route::get('/wiki', function () {echo 'Wiki/Index';})->name('wiki.index');
Route::get('/companies', function () {echo 'Companies/Index';})->name('companies.index');
Route::get('/groups', function () {echo 'Groups/Index';})->name('groups.index');
Route::get('/audit-log', function () {echo 'AuditLog/Index';})->name('audit-log.index');