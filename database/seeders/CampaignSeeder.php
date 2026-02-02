<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\Department;
use App\Models\Agreement;
use App\Models\Store;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = Status::all();
        $departments = Department::all();
        $agreements = Agreement::all();
        $store = Store::all();

        Campaign::factory()
            ->count(30)
            ->recycle($statuses)
            ->recycle($departments)
            ->recycle($agreements)
            ->create()
            ->each(function ($campaign) use ($store) {

                $campaign->stores()->attach(
                    $store->random(rand(1, 3))->mapWithKeys(function ($store) {

                        $createdAt = fake()->dateTimeBetween('-1 year', 'now');
                        $updatedAt = fake()->dateTimeBetween($createdAt, 'now');

                        return [
                            $store->getKey() => [
                                'created_at' => $createdAt,
                                'updated_at' => $updatedAt,
                            ]
                        ];
                    })->toArray()
                );
            });
    }
}