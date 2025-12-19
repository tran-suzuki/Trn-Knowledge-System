<?php

namespace App\Infrastructure\Company;

use App\Domain\Company\Company;
use App\Domain\Company\CompanyRepositoryInterface;
use App\Models\MtCompany;

class CompanyRepository implements CompanyRepositoryInterface {
	/**
	 * @return Company[]
	 */
	public function getActive(): array {
		return MtCompany::query()
			->whereNull('deleted_at')
			->where('status', 'active')
			->orderBy('name')
			->get(['id', 'name'])
			->map(fn(MtCompany $model) => new Company(
				id: $model->id,
				name: $model->name,
			))
			->all();
	}
}
