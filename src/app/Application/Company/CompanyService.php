<?php

namespace App\Application\Company;

use App\Domain\Company\Company;
use App\Domain\Company\CompanyRepositoryInterface;

class CompanyService {
	public function __construct(
		private CompanyRepositoryInterface $companyRepository
	) {}

	/**
	 *
	 * @return array
	 */
	public function getActiveCompanies(): array {
		return array_map(
			static function (Company $c): array {
				return [
					'id'   => $c->id,
					'name' => $c->name,
				];
			},
			$this->companyRepository->getActive()
		);
	}
}
