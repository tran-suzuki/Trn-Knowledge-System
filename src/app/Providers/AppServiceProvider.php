<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Register any application services.
	 */
	public function register(): void {
		$this->app->bind(
			\App\Domain\User\UserRepositoryInterface::class,
			\App\Infrastructure\User\UserRepository::class
		);
		$this->app->bind(
			\App\Domain\Company\CompanyRepositoryInterface::class,
			\App\Infrastructure\Company\CompanyRepository::class
		);
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void {
		//
	}
}
