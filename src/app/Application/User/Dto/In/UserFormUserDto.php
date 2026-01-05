<?php

namespace App\Application\User\Dto\In;

class UserFormUserDto {
	public function __construct(
		public int $id,
		public string $displayId,
		public int $fkCompanyId,
		public string $name,
		public string $nameKana,
		public string $email,
		public string $role,
		public string $status,
		public int $lockVersion,
	) {}

	public function toArray(): array {
		return [
			'id'            => $this->id,
			'display_id'    => $this->displayId,
			'fk_company_id' => $this->fkCompanyId,
			'name'          => $this->name,
			'name_kana'     => $this->nameKana,
			'email'         => $this->email,
			'role'          => $this->role,
			'status'        => $this->status,
			'lock_version'  => $this->lockVersion,
		];
	}
}
