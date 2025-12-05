<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MtGroupsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $groups = [
            ['name' => '営業部',     'company_id' => 1],
            ['name' => '開発部',     'company_id' => 1],
            ['name' => '人事部',     'company_id' => 2], 
            ['name' => 'マーケティング部', 'company_id' => 3], 
            ['name' => '総務部',     'company_id' => 4], 
            ['name' => '経理部',     'company_id' => 5],
        ];

        $display = 1;

        foreach ($groups as $group) {
            DB::table('mt_groups')->insert([
                'display_id'     => $display++, 
                'name'           => $group['name'],
                'fk_company_id'  => $group['company_id'],
                'description'    => null,
                'status'         => 'active',
                'lock_version'   => 1,
                'fk_created_by'  => null,
                'fk_updated_by'  => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }
}
