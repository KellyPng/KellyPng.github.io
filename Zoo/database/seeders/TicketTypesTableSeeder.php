<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ticketTypeExists = DB::table('ticket_types')->where('id', 1)->exists();

        if (!$ticketTypeExists) {
            DB::table('ticket_types')->insert([
                'id' => 1,
                'ticketTypeName' => 'Single Park',
            ]);
            $this->command->info('Ticket Type added to the ticket_types table!');
        } else {
            $this->command->info('Ticket type exists. Skipping seed.');
        }
    }
}
