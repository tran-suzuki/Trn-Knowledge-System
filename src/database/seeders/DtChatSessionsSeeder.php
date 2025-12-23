<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DtChatSessionsSeeder extends Seeder {
	public function run(): void {
		$now = Carbon::now();

		$groups = DB::table('mt_groups')
			->select('id', 'name', 'fk_company_id')
			->orderBy('id')
			->limit(5)
			->get();

		$users = DB::table('mt_users')
			->select('id', 'fk_company_id')
			->get();

		$display = 1;

		foreach ($groups as $group) {
			$user = $users->firstWhere('fk_company_id', $group->fk_company_id);
			if (!$user) {
				continue;
			}

			DB::table('dt_chat_sessions')->insert([
				'display_id'    => str_pad((string) $display++, 8, '0', STR_PAD_LEFT),
				'title'         => $group->name . '：最近の相談',
				'fk_user_id'    => $user->id,
				'fk_company_id' => $group->fk_company_id,
				'fk_group_id'   => $group->id,
				'fk_created_by' => $user->id,
				'fk_updated_by' => $user->id,
				'lock_version'  => 1,
				'created_at'    => $now,
				'updated_at'    => $now,
			]);
		}
	}
}
