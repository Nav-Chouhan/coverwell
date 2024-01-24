<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PaymentCategoriesSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            'key' => "payment_categories",
            'name' => "payment_categories",
            'description' => "payment_categories",
            'field' => '{"name":"value","label":"Value","type":"text"}',
            'active' => 1,
        ]);
    }
}
