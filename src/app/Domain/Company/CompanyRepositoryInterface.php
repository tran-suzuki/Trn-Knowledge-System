<?php

namespace App\Domain\Company;

interface CompanyRepositoryInterface {
	/**
	 *
	 * @return Company[]
	 */
	public function getActive(): array;
}
