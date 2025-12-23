<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
	use WithoutModelEvents;

	/**
	 * Seed the application's database.
	 */
	public function run(): void {

		$this->call([
			MtCompaniesSeeder::class,
			MtUsersSeeder::class,
			MtGroupsSeeder::class,
			DtGroupUserSeeder::class,
			DtDocumentsSeeder::class,
			DtChatSessionsSeeder::class,
			DtChatMessagesSeeder::class,
		]);
	}
}
