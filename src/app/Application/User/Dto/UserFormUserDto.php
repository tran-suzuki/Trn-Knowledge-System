<?php

namespace App\Application\User\Dto;

class UserFormUserDto {
	public function __construct(
		public int $id,
		public ?int $fk_company_id,
		public string $name,
		public string $email,
		public ?string $new_email,
		public string $role,
		public string $status,
		public int $lock_version,
		public string $display_id,
	) {}

	public function toArray(): array {
		return [
			'id'            => $this->id,
			'fk_company_id' => $this->fk_company_id,
			'name'          => $this->name,
			'email'         => $this->email,
			'new_email'     => $this->new_email,
			'role'          => $this->role,
			'status'        => $this->status,
			'lock_version'  => $this->lock_version,
			'display_id'    => $this->display_id,
		];
	}
}
