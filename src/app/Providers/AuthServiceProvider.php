<?php

namespace App\Providers;

use App\Models\MtGroup;
use App\Models\MtUser;
use App\Policies\MtGroupPolicy;
use App\Policies\MtUserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
	protected $policies = [
		MtUser::class  => MtUserPolicy::class,
		MtGroup::class => MtGroupPolicy::class,
	];

	public function boot(): void {
		$this->registerPolicies();
	}
}
