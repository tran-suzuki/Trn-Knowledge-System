<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DtGroupUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Group list → User list
        $groupUsers = [
            1 => [1, 2, 3],
            2 => [2, 3, 4],
            3 => [5, 6],
            4 => [7],
            5 => [8],
            6 => [9, 10],
        ];

        foreach ($groupUsers as $groupId => $userIds) {

            foreach ($userIds as $index => $userId) {

                if ($index === 0) {
                    $role = 'manager';
                } elseif ($index === 1) {
                    $role = 'member';
                } else {
                    $role = 'guest';
                }

                DB::table('dt_group_user')->insert([
                    'fk_group_id'   => $groupId,
                    'fk_user_id'    => $userId,
                    'fk_created_by' => 1,  // admin ID
                    'role'          => $role,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
    }
}
