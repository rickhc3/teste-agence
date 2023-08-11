<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class DataSeedCaol extends Seeder
{
    public function run()
    {
        $sql = File::get(base_path('database/banco_de_dados.sql'));
        DB::unprepared($sql);
    }
}
