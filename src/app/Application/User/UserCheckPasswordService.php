<?php

namespace App\Application\User;

use App\Models\MtUser;
use Illuminate\Support\Facades\Hash;

class UserCheckPasswordService {
	public function handle(MtUser $user, string $password) {
		return Hash::check($password, $user->password);
	}
}
