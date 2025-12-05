<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MtUsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            ['name' => '松本太郎', 'email' => 'matsumoto@example.com', 'role' => 'admin',   'company_id' => 1],
            ['name' => '中村花子', 'email' => 'nakamura@example.com', 'role' => 'manager', 'company_id' => 1],
            ['name' => '小林健一', 'email' => 'kobayashi@example.com', 'role' => 'user',   'company_id' => 2],
            ['name' => '加藤直樹', 'email' => 'kato@example.com',       'role' => 'user',   'company_id' => 2],
            ['name' => '山本彩',   'email' => 'yamamoto@example.com',   'role' => 'manager','company_id' => 3],
            ['name' => '吉田翔',   'email' => 'yoshida@example.com',    'role' => 'user',   'company_id' => 3],
            ['name' => '石井優',   'email' => 'ishii@example.com',      'role' => 'user',   'company_id' => 4],
            ['name' => '山田涼',   'email' => 'yamada@example.com',     'role' => 'manager','company_id' => 4],
            ['name' => '岡本海斗', 'email' => 'okamoto@example.com',    'role' => 'user',   'company_id' => 5],
            ['name' => '清水愛',   'email' => 'shimizu@example.com',    'role' => 'admin',  'company_id' => 5],
        ];

        $display = 1;

        foreach ($users as $user) {
            DB::table('mt_users')->insert([
                'display_id'         => $display++, 
                'name'               => $user['name'],
                'email'              => $user['email'],
                'password'           => Hash::make('password'),
                'role'               => $user['role'],
                'status'             => 'active',
                'fk_company_id'      => $user['company_id'],
                'lock_version'       => 1,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }
}
