<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = [
            ['name' => 'Tarox'],
            ['name' => 'Jarltech'],
            ['name' => 'CDS'],
            ['name' => 'Comline'],
            ['name' => 'Allnet'],
        ];

        DB::table('companies')->insert($companies);
    }
}
