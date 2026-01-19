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
            ['name' => '松本太郎','name_kana' => '松本太郎_kana', 'email' => 'matsumoto@example.com', 'role' => 'admin',   'company_id' => 1],
            ['name' => '中村花子','name_kana' => '中村花子_kana', 'email' => 'nakamura@example.com', 'role' => 'manager', 'company_id' => 1],
            ['name' => '小林健一','name_kana' => '小林健一_kana', 'email' => 'kobayashi@example.com', 'role' => 'user',   'company_id' => 2],
            ['name' => '加藤直樹','name_kana' => '加藤直樹_kana', 'email' => 'kato@example.com',       'role' => 'user',   'company_id' => 2],
            ['name' => '山本彩',  'name_kana' => '山本彩_kana',   'email' => 'yamamoto@example.com',   'role' => 'manager','company_id' => 3],
            ['name' => '吉田翔',  'name_kana' => '吉田翔_kana',   'email' => 'yoshida@example.com',    'role' => 'user',   'company_id' => 3],
            ['name' => '石井優',  'name_kana' => '石井優_kana',   'email' => 'ishii@example.com',      'role' => 'user',   'company_id' => 4],
            ['name' => '山田涼',  'name_kana' => '山田涼_kana',   'email' => 'yamada@example.com',     'role' => 'manager','company_id' => 4],
            ['name' => '岡本海斗','name_kana' => '岡本海斗_kana', 'email' => 'okamoto@example.com',    'role' => 'user',   'company_id' => 5],
            ['name' => '清水愛',  'name_kana' => '清水愛_kana',   'email' => 'shimizu@example.com',    'role' => 'admin',  'company_id' => 5],
        ];

        $display = 1;

        foreach ($users as $user) {
            DB::table('mt_users')->insert([
                'display_id'         => $display++, 
                'name'               => $user['name'],
                'name_kana'               => $user['name_kana'],
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
