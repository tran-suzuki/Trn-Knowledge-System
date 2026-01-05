<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DtOperationLogsSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 */
	public function run(): void {
		$now = now();

		$userIds = DB::table('mt_users')->limit(10)->pluck('id')->all();

		$actions     = ['LOGIN', 'LOGOUT', 'FILE_UPLOAD', 'FILE_DELETE', 'PERMISSION_CHANGE'];
		$targetTypes = ['dt_documents', 'mt_groups', 'mt_users'];

		$rows = [];
		for ($i = 1; $i <= 50; $i++) {
			$fkUserId = $userIds ? $userIds[array_rand($userIds)] : null;

			$action     = $actions[array_rand($actions)];
			$targetType = $targetTypes[array_rand($targetTypes)];
			$targetId   = $targetType ? random_int(1, 200) : null;

			$rows[] = [
				'display_id'  => $i,
				'created_at'  => $now->copy()->subMinutes(50 - $i),
				'fk_user_id'  => $fkUserId,
				'action'      => $action,
				'target_type' => $targetType,
				'target_id'   => $targetId,
				'details'     => json_encode([
					'message' => 'seed',
					'before'  => ['status' => 'old'],
					'after'   => ['status' => 'new'],
				], JSON_UNESCAPED_UNICODE),
				'ip_address'  => '127.0.0.1',
				'user_agent'  => 'Seeder/Local',
			];
		}

		DB::table('dt_operation_logs')->insert($rows);
	}
}
