<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Enums\CampaignStatus;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = CampaignStatus::cases();

        foreach ($statuses as $status) {
            Status::create(['status' => $status->value]);
        }
    }
}
