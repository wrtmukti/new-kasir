<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Outlet;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [
            [
                'outlet_name' => 'Geprek Gambos',
                'outlet_code' => 'GGB',
                'outlet_branch' => 'Jakarta',
                'outlet_slug' => 'geprek-gambos',
                'outlet_email' => 'geprek@gambos.com',
                'outlet_phone' => '021-12345678',
                'outlet_address' => 'Jl. Merdeka No. 10, Jakarta Pusat',
                'outlet_status' => 1,
            ],
            [
                'outlet_name' => 'Geprek Gambos',
                'outlet_code' => 'GGB',
                'outlet_branch' => 'Yogyakarta',
                'outlet_slug' => 'geprek-gambos-jogja',
                'outlet_email' => 'geprek.jogja@gambos.com',
                'outlet_phone' => '0274-87654321',
                'outlet_address' => 'Jl. Malioboro No. 25, Yogyakarta',
                'outlet_status' => 1,
            ],
            [
                'outlet_name' => 'Bakso Malang Cak Udin',
                'outlet_code' => 'BMC',
                'outlet_branch' => 'Surabaya',
                'outlet_slug' => 'bakso-malang-cak-udin',
                'outlet_email' => 'bakso@cakudin.com',
                'outlet_phone' => '031-5557777',
                'outlet_address' => 'Jl. Panglima Sudirman No. 5, Surabaya',
                'outlet_status' => 1,
            ],
        ];

        foreach ($outlets as $data) {
            Outlet::create($data);
        }
    }
}
