<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Promo;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Promo::truncate();

        $promos = [
            [
                'id' => 'PRM-001',
                'kode_promo' => 'WEEKEND',
                'syarat_promo' => 'Valid on Saturday and Sunday',
                'jenis_promo' => 'Weekend Discount',
                'status_promo' => 'Available',
                'besar_potongan' => 15,
                
            ],
            [
                'id' => 'PRM-002',
                'kode_promo' => 'BIRTHDAY',
                'syarat_promo' => "Valid when the customer's birthda",
                'jenis_promo' => 'Birthday Discount',
                'status_promo' => 'Available',
                'besar_potongan' => 10,
            ],
            [
                'id' => 'PRM-003',
                'kode_promo' => 'HUTRI',
                'syarat_promo' => 'Valid on Indonesian independence day',
                'jenis_promo' => "17an Discount",
                'status_promo' => 'Unavailable',
                'besar_potongan' => 17,
            ],
            [
                'id' => 'PRM-004',
                'kode_promo' => 'HAPPYENDMONTH',
                'syarat_promo' => 'Valid from 5 days before the end of the month',
                'jenis_promo' => "End month Discount",
                'status_promo' => 'Available',
                'besar_potongan' => 5,
            ],

        ];

        Promo::insert($promos);
    }
}
