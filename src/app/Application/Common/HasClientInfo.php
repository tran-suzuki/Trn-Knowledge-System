<?php

namespace App\Application\Common;

use Illuminate\Http\Request;

trait HasClientInfo {
	private function getClientIp(Request $request): ?string {
		$ip = $request->header('CF-Connecting-IP') ?? $this->firstFromXForwardedFor($request->header('X-Forwarded-For')) ?? $request->header('X-Real-IP') ?? $request->ip();

		return $this->normalizeIp($ip);
	}

	private function getUserAgent(Request $request): ?string {
		$ua = $request->userAgent();
		$ua = $ua ? trim($ua) : null;

		return $ua !== '' ? $ua : null;
	}

	private function firstFromXForwardedFor(?string $xff): ?string {
		if (!$xff) {
			return null;
		}

		return trim(explode(',', $xff)[0] ?? '');
	}

	private function normalizeIp(?string $ip): ?string {
		if (!$ip) {
			return null;
		}

		$ip = trim($ip);
		if ($ip === '') {
			return null;
		}

		// IPv4-mapped IPv6 ::ffff:127.0.0.1
		if (str_starts_with($ip, '::ffff:')) {
			$ip = substr($ip, 7);
		}

		return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
	}
}
