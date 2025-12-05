<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserAccountIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        $user->loadMissing(['company', 'groups']);

        if ($user->deleted_at !== null || $user->status !== 'active') {
            return $this->logout($request);
        }

        if (!$user->company || $user->company->deleted_at !== null) {
            return $this->logout($request);
        }

        $hasActiveGroup = $user->groups()
            ->whereNull('mt_groups.deleted_at')
            ->where('mt_groups.status', 'active')
            ->exists();

        if (!$hasActiveGroup) {
            return $this->logout($request);
        }

        return $next($request);
    }

    private function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->setRememberToken(null);
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors(['email' => 'アカウントが無効になっています。']);
    }
}
