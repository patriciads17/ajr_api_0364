<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::truncate();

        $employees = [
            [
                'id' => 'ADM-001',
                'nama_pegawai' => 'Karina Widjaya',
                'idRole' => 'ADM' ,
                'alamat_pegawai' => 'Bandung',
                'tgl_lahir_pegawai' => '1999-10-11',
                'gender_pegawai' => 'Female',
                'no_telp_pegawai' => '082292208002',
                'email' => 'karina@gmail.com',
                'password' => '$2a$12$z4kHwW1RRWBCvOZnoXNlIOny9pKy028ccRIWrb1Wd.gHwY0k/f1Um',
            ],
            [
                'id' => 'CSV-001',
                'nama_pegawai' => 'Bagas Purnomo',
                'idRole' => 'CSV' ,
                'alamat_pegawai' => 'Surabaya',
                'tgl_lahir_pegawai' => '1997-02-28',
                'gender_pegawai' => 'Male',
                'no_telp_pegawai' => '082291898002',
                'email' => 'bagas@gmail.com',
                'password' => '$2a$12$M2YSj1JiWKJuWefHqawi/eFUcSOBUZf4EjFYRWrwFe7Fc8lWm694W',
            ],
            [
                'id' => 'MGR-001',
                'nama_pegawai' => 'Fadilla Astrid',
                'idRole' => 'MGR' ,
                'alamat_pegawai' => 'Malang',
                'tgl_lahir_pegawai' => '1995-12-17',
                'gender_pegawai' => 'Female',
                'no_telp_pegawai' => '082198898002',
                'email' => 'fadilla@gmail.com',
                'password' => '$2a$12$U3JOMbyBnl4TfqVNUbBeiu9xyO1TX9gcILfNCN5HwEcSf3ClWurGu',
            ],
        ];

        Employee::insert($employees);
    }
}
