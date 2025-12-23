<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\GroupMember\GroupMemberController;
use App\Http\Controllers\Group\GroupController;

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

Route::middleware(['auth'])->prefix('users')->name('user.')->group(function () {
	Route::get('/', [UserController::class, 'index'])->name('index');
	Route::get('/create', [UserController::class, 'create'])->name('create');
	Route::post('/', [UserController::class, 'store'])->name('store');
	Route::get('/{user:display_id}/edit', [UserController::class, 'edit'])->name('edit');
	Route::put('/{user:display_id}', [UserController::class, 'update'])->name('update');
	Route::delete('/{user:display_id}', [UserController::class, 'destroy'])->name('destroy');
	Route::post('/{user:display_id}/check-lock', [UserController::class, 'checkLock'])->name('checkLock');
	Route::post('/{user:display_id}/check-password', [UserController::class, 'checkPassword'])->name('checkPassword');
	Route::get('/email-verify-change', [UserController::class, 'confirmEmailChange'])->name('email-verify-change');
});

Route::middleware(['auth'])->prefix('groups')->name('groups.')->group(function () {
	Route::get('/', [GroupController::class, 'index'])->name('index');
	Route::get('/create', [GroupController::class, 'create'])->name('create');
	Route::post('/', [GroupController::class, 'store'])->name('store');
	Route::get('/{mtGroup:display_id}', [GroupController::class, 'show'])->name('show');
	Route::delete('/{mtGroup:display_id}', [GroupController::class, 'destroy'])->name('destroy');
	Route::post('/{mtGroup:display_id}/check-lock-version', [GroupController::class, 'checkLock'])->name('check.lock_version');

	Route::prefix('/{mtGroup:display_id}/members')->name('members.')->group(function () {
		Route::get('/', [GroupMemberController::class, 'index'])->name('index');
		Route::get('/addable-members', [GroupMemberController::class, 'listAddableMembers'])->name('list_addable');
		Route::post('/', [GroupMemberController::class, 'store'])->name('store');
		Route::patch('/', [GroupMemberController::class, 'updateMembersRole'])->name('bulk_update_role');
		Route::patch('/{member:display_id}', [GroupMemberController::class, 'updateMemberRole'])->name('update_role');
		Route::delete('/{member:display_id}', [GroupMemberController::class, 'destroy'])->name('destroy');
		Route::post('/{member:display_id}/check-lock-version', [GroupMemberController::class, 'checkLock'])->name('check.lock_version');
	});
});

Route::middleware(['auth'])->prefix('chats')->name('chats.')->group(function () {
	Route::get('/group/{mtGroup:display_id}', [ChatController::class, 'index'])->name('index');
});

//todo
Route::get('/knowledge', function () {echo 'Knowledge/Index';})->name('knowledge.index');
Route::get('/folders', function () {echo 'Folders/Index';})->name('folders.index');
Route::get('/wiki', function () {echo 'Wiki/Index';})->name('wiki.index');
Route::get('/companies', function () {echo 'Companies/Index';})->name('companies.index');
Route::get('/audit-log', function () {echo 'AuditLog/Index';})->name('audit-log.index');