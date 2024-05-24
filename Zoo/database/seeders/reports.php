<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmployeeReports; 
use Faker\Factory as Faker;

class reports extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 25) as $index) {
            EmployeeReports::create([
                'SUBJECT' => $faker->sentence,
                'description' => $faker->paragraph,
                'email' => $faker->safeEmail,
                'image' => 'report.jpg', 
            ]);
        }
    }
}