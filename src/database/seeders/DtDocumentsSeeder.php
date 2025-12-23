<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DtDocumentsSeeder extends Seeder {
	public function run(): void {
		$now = Carbon::now();

		$groups = DB::table('mt_groups')
			->whereNull('deleted_at')
			->select(['id', 'display_id'])
			->orderBy('id')
			->get();

		if ($groups->isEmpty()) {
			return;
		}

		$adminId = DB::table('mt_users')
			->whereNull('deleted_at')
			->orderBy('id')
			->value('id');

		$maxDisplay = DB::table('dt_documents')
			->selectRaw('MAX(CAST(display_id AS UNSIGNED)) AS max_display')
			->value('max_display');

		$display = (int) ($maxDisplay ?? 0);

		foreach ($groups as $g) {
			$display++;
			$rootDisplayId = str_pad((string) $display, 8, '0', STR_PAD_LEFT);

			$rootId = DB::table('dt_documents')->insertGetId([
				'display_id'   => $rootDisplayId,
				'fk_parent_id' => null,
				'fk_user_id'   => null,
				'fk_group_id'  => $g->id,
				'type'         => 'folder',
				'name'         => 'root',
				'path'         => "/groups/{$g->display_id}",
				'size'          => null,
				'mime_type'     => null,
				'lock_version'  => 1,
				'fk_created_by' => $adminId,
				'fk_updated_by' => $adminId,
				'created_at'    => $now,
				'updated_at'    => $now,
				'deleted_at'    => null,
			]);

			for ($i = 1; $i <= 5; $i++) {
				$display++;
				$docDisplayId = str_pad((string) $display, 8, '0', STR_PAD_LEFT);

				$fileName = "doc_{$g->id}_{$i}.pdf";

				DB::table('dt_documents')->insert([
					'display_id'   => $docDisplayId,
					'fk_parent_id' => $rootId,
					'fk_user_id'   => null,
					'fk_group_id'  => $g->id,
					'type'         => 'file',
					'name'         => $fileName,
					'path'         => "/groups/{$g->display_id}/{$fileName}",
					'size'          => random_int(50_000, 5_000_000),
					'mime_type'     => 'application/pdf',
					'lock_version'  => 1,
					'fk_created_by' => $adminId,
					'fk_updated_by' => $adminId,
					'created_at'    => $now,
					'updated_at'    => $now,
					'deleted_at'    => null,
				]);
			}
		}
	}
}
