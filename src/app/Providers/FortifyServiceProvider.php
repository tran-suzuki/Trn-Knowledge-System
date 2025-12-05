<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\MtUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\Auth\LoginRequest as AppLoginRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FortifyLoginRequest::class, AppLoginRequest::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Login画面
        Fortify::loginView(function (Request $request): mixed {
            return Inertia::render('Auth/Login', [
                'status'           => $request->session()->get('status'),
                'canResetPassword' => Route::has('password.request'),
            ]);
        });

        Fortify::authenticateUsing(function (Request $request) {

            $user = MtUser::findForLogin($request->email);
            
            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['メールアドレスとパスワードを正しく入力してください。'],
                ]);
            }

            return $user;
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect()->intended(route('dashboard'));
            }
        });

        // パスワード再設定メール送信画面
        Fortify::requestPasswordResetLinkView(function (Request $request): mixed {
            return Inertia::render('Auth/ForgotPassword', [
                'status' => $request->session()->get('status'),
            ]);
        });

        // パスワード再設定フォーム画面
        Fortify::resetPasswordView(function ($request) {
            return Inertia::render('Auth/ResetPassword', [
                'token' => $request->route('token'),
                'email' => $request->email,
            ]);
        });
        
        /*
        |--------------------------------------------------------------------------
        | レスポンスのカスタマイズ
        |--------------------------------------------------------------------------
        */

         /*
        |--------------------------------------------------------------------------
        | Fortify アクション / レート制限（※現時点では未使用）
        |--------------------------------------------------------------------------
        */
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
