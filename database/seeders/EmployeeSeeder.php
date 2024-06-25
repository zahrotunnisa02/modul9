<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('employees')->insert([
        //     [
        //         'firstname' => 'Purnama',
        //         'lasstname' => 'Anaking',
        //         'email' => 'purnama.anaking@gmail.com',
        //         'age' => 40,
        //         'position_id' => 1
        //     ],
        //     [
        //         'firstname' => 'Adzanil',
        //         'lastname' => 'Rachmadi',
        //         'email' => 'adzanil.rachmadi@gmail.com',
        //         'age' => 25,
        //         'position_id' => 2
        //     ],
        //     [
        //         'firstname' => 'Berlian',
        //         'lastname' => 'Rachmi',
        //         'email' => 'berlian.rachmi@gmail.com',
        //         'age' => 23,
        //         'position_id' => 3
        //     ],
        // ]);
        Employee::factory()->count(10)->create();
    }
}
