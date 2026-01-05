<?php

namespace App\Application\OperationLog;

use App\Domain\OperationLog\In\OperationLogStoreInput;
use App\Domain\OperationLog\OperationLogRepositoryInterface;
use Illuminate\Support\Str;

class OperationLogRegisterService {
	public function __construct(
		private OperationLogRepositoryInterface $operationLogRepository,
	) {}

	public function success(
		int $actorId,
		string $action,
		string $targetType,
		?int $targetId,
		array $details = [],
	): void {
		$this->write(
			actorId: $actorId,
			action: $action,
			targetType: $targetType,
			targetId: $targetId,
			details: $details,
		);
	}

	public function failed(
		int $actorId,
		string $action,
		string $targetType,
		?int $targetId,
		array $details = []
	): void {
		$this->write(
			actorId: $actorId,
			action: $action,
			targetType: $targetType,
			targetId: $targetId,
			details: $details,
		);
	}

	private function write(
		int $actorId,
		string $action,
		string $targetType,
		?int $targetId,
		array $details
	): void {
		$input = new OperationLogStoreInput(
			displayId: $this->generateUniqueDisplayId(),
			fkUserId: $actorId,
			action: $action,
			targetType: $targetType,
			targetId: $targetId,
			details: $details,
			ipAddress: $this->resolveIp(),
			userAgent: $this->resolveUserAgent(),
		);

		$this->operationLogRepository->create($input);
	}

	private function resolveIp(): ?string {
		$ip = request()->header('CF-Connecting-IP') ?? $this->firstFromXForwardedFor(request()->header('X-Forwarded-For')) ?? request()->header('X-Real-IP') ?? request()->ip();

		$ip = is_string($ip) ? trim($ip) : null;
		if (!$ip) {
			return null;
		}

		// IPv4-mapped IPv6 ::ffff:127.0.0.1
		if (str_starts_with($ip, '::ffff:')) {
			$ip = substr($ip, 7);
		}

		return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
	}

	private function firstFromXForwardedFor(?string $xff): ?string {
		if (!$xff) {
			return null;
		}

		$first = trim(explode(',', $xff)[0] ?? '');
		return $first !== '' ? $first : null;
	}

	private function resolveUserAgent(): ?string {
		$ua = request()->userAgent();
		$ua = is_string($ua) ? trim($ua) : null;

		if ($ua !== '') {
			if (str_contains($ua, 'Edg/')) {
				$browser = 'Microsoft Edge';
			} elseif (str_contains($ua, 'Chrome/')) {
				$browser = 'Google Chrome';
			} elseif (str_contains($ua, 'Safari/')) {
				$browser = 'Safari';
			} elseif (str_contains($ua, 'Firefox/')) {
				$browser = 'Firefox';
			} else {
				$browser = 'Other';
			}
		}
		return $browser;
	}

	private function generateUniqueDisplayId(): string {
		for ($i = 0; $i < 10; $i++) {
			$displayId = Str::random(8);

			if (!$this->operationLogRepository->existsByDisplayId($displayId)) {
				return $displayId;
			}
		}

		throw new \RuntimeException(__('group.display_id_exist'));
	}
}
