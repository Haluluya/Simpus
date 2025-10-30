<?php

namespace Database\Seeders;

use App\Models\QueueTicket;
use Illuminate\Database\Seeder;

class QueueTicketSeeder extends Seeder
{
    public function run(): void
    {
        QueueTicket::factory()->count(40)->create();
    }
}
