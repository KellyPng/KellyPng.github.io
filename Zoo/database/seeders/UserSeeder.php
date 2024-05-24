<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                'firstname' => 'Admin',
                'lastname' => 'Admin',
                'employeeType' => 'Top Management',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('#Admin123'),
            ]);

            $this->command->info('User added to the users table!');
        } else {
            $this->command->info('Users table is not empty. Skipping seed.');
        }
    }
}
