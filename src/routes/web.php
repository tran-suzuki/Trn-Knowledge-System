<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\ChatSessionController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\GroupMember\GroupMemberController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\OperationLog\OperationLogController;
use App\Http\Controllers\User\UserController;
use App\Application\GoogleCloud\RagService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	if (auth()->check()) {
		return redirect()->route('dashboard');
	}

	return redirect()->route('login');
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
	->name('password.email');

Route::middleware(['auth', 'checkUserAccountIsValid'])->group(function () {
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
	Route::get('/dashboard/group', [DashboardController::class, 'groups'])->name('dashboard.groups');
	Route::get('/dashboard/chat-session', [DashboardController::class, 'chatSessions'])->name('dashboard.chat_sessions');
});

Route::middleware(['auth'])->prefix('users')->name('users.')->group(function () {
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
		Route::delete('/{member:display_id}', [GroupMemberController::class, 'destroy'])->name('destroy');
		Route::post('/{member:display_id}/check-lock-version', [GroupMemberController::class, 'checkLock'])->name('check.lock_version');
	});
});

Route::middleware(['auth'])->prefix('chats')->name('chats.')->group(function () {
	Route::get('/group/{mtGroup:display_id}', [ChatController::class, 'index'])->name('index');
	Route::get('/session/{dtChatSession:display_id}', [ChatController::class, 'sessionIndex'])->name('session.index');
	Route::get('/session/{dtChatSession:display_id}/messages', [ChatController::class, 'messages'])->name('session.messages');
	Route::post('/ask', [ChatController::class, 'askRag'])->name('ask');
});

Route::middleware(['auth'])->prefix('audit-logs')->name('audit_logs.')->group(function () {
	Route::get('/', [OperationLogController::class, 'index'])->name('index');
	Route::get('/{dtOperationLog:display_id}', [OperationLogController::class, 'detail'])->name('detail');
});

Route::middleware(['auth'])->prefix('documents')->name('documents.')->group(function () {
	Route::get('/', [DocumentController::class, 'index'])->name('index');
	Route::get('/search', [DocumentController::class, 'search'])->name('search');
	Route::get('/tree', [DocumentController::class, 'tree'])->name('tree');
	Route::post('/copy', [DocumentController::class, 'copy'])->name('copy');
	Route::post('/store', [DocumentController::class, 'store'])->name('store');
	Route::delete('/', [DocumentController::class, 'destroy'])->name('destroy');
	Route::post('/check-upload-conflicts', [DocumentController::class, 'checkUploadConflicts'])->name('check.upload_conflicts');
	Route::post('/{dtDocuments:display_id}/check-lock-version', [DocumentController::class, 'checkLock'])->name('check.lock_version');
	Route::get('/check-copy-conflicts', [DocumentController::class, 'checkCopyConflicts'])->name('check.copy_conflicts');
});

//todo
Route::get('/folders', function () {echo 'Folders/Index';})->name('folders.index');