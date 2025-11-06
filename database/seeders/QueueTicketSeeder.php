<?php

namespace Database\Seeders;

use App\Models\QueueTicket;
use Illuminate\Database\Seeder;

class QueueTicketSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all records to avoid duplicates
        QueueTicket::query()->delete();

        QueueTicket::factory()->count(40)->create();
    }
}
