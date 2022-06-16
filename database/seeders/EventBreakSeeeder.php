<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventBreak;

class EventBreakSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $breaks = [
            'lunch' => [
                'starts_at' => '12:00',
                'ends_at' => '13:00',
            ],
            'cleaning_break' => [
                'starts_at' => '15:00',
                'ends_at' => '16:00',
            ],
        ];

        foreach ($breaks as $title => $times) {
            EventBreak::create([
                'title' => $title,
                'starts_at' => $times['starts_at'],
                'ends_at' => $times['ends_at'],
            ]);
        }
    }
}
