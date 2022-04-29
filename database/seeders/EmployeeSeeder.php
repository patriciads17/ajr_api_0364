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
                'id' => 'Test',
                'nama_pegawai' => 'Karina Widjaja',
                'idRole' => 'ADM' ,
                'alamat_pegawai' => 'Bandung',
                'tgl_lahir_pegawai' => '1999-10-11',
                'gender_pegawai' => 'Female',
                'no_telp_pegawai' => '082292208002',
                'email' => 'karina@gmail.com',
                'password' => '$2a$12$iFxF./zX8dSqPMwkEeR0.uOZpJUc9kJ0w/eCIhYeSxrNiPyLCVn7i',
            ],
        ];

        Employee::insert($employees);
    }
}
