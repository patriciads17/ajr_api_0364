<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shift::truncate();

        $shifts = [
            [
                'id' => 'TUE-01',
                'hari_shift' => 'Tuesday',
                'jadwal_shift' => 'Session1'
            ],
            [
                'id' => 'TUE-02',
                'hari_shift' => 'Tuesday',
                'jadwal_shift' => 'Session2'
            ],
            [
                'id' => 'WED-01',
                'hari_shift' => 'Wednesday',
                'jadwal_shift' => 'Session1'
            ],
            [
                'id' => 'WED-02',
                'hari_shift' => 'Wednesday',
                'jadwal_shift' => 'Session2'
            ],
            [
                'id' => 'THU-01',
                'hari_shift' => 'Thursday',
                'jadwal_shift' => 'Session1'
            ],
            [
                'id' => 'THU-02',
                'hari_shift' => 'Thursday',
                'jadwal_shift' => 'Session2'
            ],
            [
                'id' => 'FRI-01',
                'hari_shift' => 'Friday',
                'jadwal_shift' => 'Session1'
            ],
            [
                'id' => 'FRI-02',
                'hari_shift' => 'Friday',
                'jadwal_shift' => 'Session2'
            ],
            [
                'id' => 'SAT-01',
                'hari_shift' => 'Saturday',
                'jadwal_shift' => 'Session1'
            ],
            [
                'id' => 'SAT-02',
                'hari_shift' => 'Saturday',
                'jadwal_shift' => 'Session2'
            ],
            [
                'id' => 'SUN-01',
                'hari_shift' => 'Sunday',
                'jadwal_shift' => 'Session1'
            ],
            [
                'id' => 'SUN-02',
                'hari_shift' => 'Sunday',
                'jadwal_shift' => 'Session2'
            ],     
        ];

        Shift::insert($shifts);
    }
}
