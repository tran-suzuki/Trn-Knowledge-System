<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MtCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $companies = [
            ['name' => '鈴木株式会社', 'status' => 'active', 'address' => '東京都品川区', 'phone_number' => '03-1234-5678'],
            ['name' => '田中ホールディングス', 'status' => 'active', 'address' => '東京都渋谷区', 'phone_number' => '03-2345-6789'],
            ['name' => '佐藤テクノロジー', 'status' => 'active', 'address' => '大阪市北区', 'phone_number' => '06-3456-7890'],
            ['name' => '高橋ソリューションズ', 'status' => 'active', 'address' => '名古屋市中区', 'phone_number' => '052-456-7890'],
            ['name' => '伊藤システムズ', 'status' => 'active', 'address' => '福岡市博多区', 'phone_number' => '092-567-8901'],
        ];

        $display = 1;

        foreach ($companies as $company) {
            DB::table('mt_companies')->insert([
                'display_id'   => $display++,  
                'name'         => $company['name'],
                'status'       => $company['status'],
                'address'      => $company['address'],
                'phone_number' => $company['phone_number'],
                'lock_version' => 1,
                'fk_created_by' => null,
                'fk_updated_by' => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
