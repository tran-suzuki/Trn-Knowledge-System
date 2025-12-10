<?php

namespace App\Providers;

use App\Models\MtUser;
use App\Policies\MtUserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
	protected $policies = [
		MtUser::class => MtUserPolicy::class,
	];

	public function boot(): void {
		$this->registerPolicies();
	}
}
