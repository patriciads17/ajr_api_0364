<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Driver;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Driver::truncate();

        $drivers = [
            [
                'id' => 'DRV-170222001',
                'nama_driver' => 'Novia Achmad',
                'alamat_driver' => 'Bandung',
                'tgl_lahir_driver' => '1996-02-11',
                'gender_driver' => 'Female',
                'no_telp_driver' => '082292208102',
                'email' => 'novia@gmail.com',
                'rerata_rating'=> '4.80',
                'kemampuan_bahasa' => 'Indonesia, Thailand',
                'tarif_harian_driver' => '120000',
                'status_ketersediaan_driver' => 'Available',
            ],
            [
                'id' => 'DRV-110522002',
                'nama_driver' => 'Nuca Santoso',
                'alamat_driver' => 'Magelang',
                'tgl_lahir_driver' => '1998-12-22',
                'gender_driver' => 'Male',
                'no_telp_driver' => '082276208102',
                'email' => 'nuca@gmail.com',
                'rerata_rating'=> '4.50',
                'kemampuan_bahasa' => 'Indonesia',
                'tarif_harian_driver' => '100000',
                'status_ketersediaan_driver' => 'Available',
            ],
            [
                'id' => 'DRV-110622002',
                'nama_driver' => 'Judika Handoko',
                'alamat_driver' => 'Surabaya',
                'tgl_lahir_driver' => '1995-12-22',
                'gender_driver' => 'Male',
                'no_telp_driver' => '082278608102',
                'email' => 'judika@gmail.com',
                'rerata_rating'=> '4.75',
                'kemampuan_bahasa' => 'Indonesia',
                'tarif_harian_driver' => '120000',
                'status_ketersediaan_driver' => 'Available',
            ],
        ];

        Driver::insert($drivers);
    }
}
