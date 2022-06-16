<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShopTime;

class ShopTimeSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $weekdays = [
            'opens_at' => '08:00',
            'closes_at' => '20:00',
        ];

        $weekend = [
            'opens_at' => '10:00',
            'closes_at' => '22:00',
        ];
        
        $weekOff = [
            'opens_at' => '00:00',
            'closes_at' => '00:00',
        ];

        $days = [
            'Monday' => $weekdays,
            'Tuesday' => $weekdays,
            'Wednesday' => $weekdays,
            'Thursday' => $weekdays,
            'Friday' => $weekdays,
            'Saturday' => $weekend,
            'Sunday' => $weekOff,
        ];

        foreach ($days as $day => $times) {
            ShopTime::create([
                'day' => $day,
                'opens_at' => $times['opens_at'],
                'closes_at' => $times['closes_at'],
            ]);
        }
    }
}
