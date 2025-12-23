<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserFormUserDto;
use App\Models\MtUser;

class UserEditService {
	public function __construct(
	) {}

	public function handle(MtUser $user): UserFormUserDto {
		return new UserFormUserDto(
			id: $user->id,
			fk_company_id: $user->fk_company_id,
			name: $user->name,
			email: $user->email,
			new_email: null,
			role: $user->role,
			status: $user->status,
			lock_version: $user->lock_version,
			display_id: $user->display_id,
		);
	}
}
