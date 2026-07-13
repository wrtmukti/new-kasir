<?php

namespace Database\Seeders;

use App\Models\SysAdmin\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'company_name' => 'Geprek Gambos',
                'company_code' => 'GGB',
                'company_branch' => 'Jakarta',
                'company_slug' => 'geprek-gambos',
                'company_email' => 'geprek@gambos.com',
                'company_phone' => '021-12345678',
                'company_address' => 'Jl. Merdeka No. 10, Jakarta Pusat',
                'company_status' => 1,
            ],
            [
                'company_name' => 'Geprek Gambos',
                'company_code' => 'GGB',
                'company_branch' => 'Yogyakarta',
                'company_slug' => 'geprek-gambos-jogja',
                'company_email' => 'geprek.jogja@gambos.com',
                'company_phone' => '0274-87654321',
                'company_address' => 'Jl. Malioboro No. 25, Yogyakarta',
                'company_status' => 1,
            ],
            [
                'company_name' => 'Bakso Malang Cak Udin',
                'company_code' => 'BMC',
                'company_branch' => 'Surabaya',
                'company_slug' => 'bakso-malang-cak-udin',
                'company_email' => 'bakso@cakudin.com',
                'company_phone' => '031-5557777',
                'company_address' => 'Jl. Panglima Sudirman No. 5, Surabaya',
                'company_status' => 1,
            ],
        ];

        foreach ($companies as $data) {
            Company::create($data);
        }
    }
}
