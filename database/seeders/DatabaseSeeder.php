<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ShopTimeSeeeder::class);
        $this->call(EventBreakSeeeder::class);
        $this->call(HolidaySeeeder::class);
        $this->call(EventSeeeder::class);
    }
}
