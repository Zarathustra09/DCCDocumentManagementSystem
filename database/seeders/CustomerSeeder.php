<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['code' => 'ALL', 'name' => 'ALLEGRO MICROSYSTEMS', 'is_active' => true],
            ['code' => 'AME', 'name' => 'AMERTRON', 'is_active' => true],
            ['code' => 'AMK', 'name' => 'AMKOR', 'is_active' => true],
            ['code' => 'AMP', 'name' => 'AMPLEON', 'is_active' => true],
            ['code' => 'ANA', 'name' => 'ANALOG DEVICES', 'is_active' => true],
            ['code' => 'ANT', 'name' => 'ANALOG TEST', 'is_active' => true],
            ['code' => 'AUT', 'name' => 'AUTOMATED TECHNOLOGY', 'is_active' => true],
            ['code' => 'BAX', 'name' => 'BAXTER', 'is_active' => true],
            ['code' => 'COH', 'name' => 'COHU', 'is_active' => true],
            ['code' => 'CYP', 'name' => 'CYPRESS', 'is_active' => true],
            ['code' => 'EAT', 'name' => 'EATON', 'is_active' => true],
            ['code' => 'EXC', 'name' => 'EXCELITAS', 'is_active' => true],
            ['code' => 'FOC', 'name' => 'FOCUSED TEST', 'is_active' => true],
            ['code' => 'IMI', 'name' => 'IMI', 'is_active' => true],
            ['code' => 'INF', 'name' => 'INFINEON', 'is_active' => true],
            ['code' => 'LAS', 'name' => 'LASER', 'is_active' => true],
            ['code' => 'LAT', 'name' => 'LATTICE', 'is_active' => true],
            ['code' => 'LIT', 'name' => 'LITTLEFUSE', 'is_active' => true],
            ['code' => 'MAX', 'name' => 'MAXIM', 'is_active' => true],
            ['code' => 'MIC', 'name' => 'MICROCHIP', 'is_active' => true],
            ['code' => 'NEP', 'name' => 'NEPES', 'is_active' => true],
            ['code' => 'ONS', 'name' => 'ONSEMICONDUCTOR SLOVAKIA AS', 'is_active' => true],
            ['code' => 'ONC', 'name' => 'ONSEMI CARMONA', 'is_active' => true],
            ['code' => 'ONT', 'name' => 'ONSEMI TARLAC', 'is_active' => true],
            ['code' => 'SAE', 'name' => 'SAE HONGKONG', 'is_active' => true],
            ['code' => 'SAP', 'name' => 'SAMPO', 'is_active' => true],
            ['code' => 'SAM', 'name' => 'SAMSUN ELECTRONICS TECHNOLOGY', 'is_active' => true],
            ['code' => 'SEA', 'name' => 'SEAGATE', 'is_active' => true],
            ['code' => 'SKY', 'name' => 'SKYWORKS', 'is_active' => true],
            ['code' => 'SON', 'name' => 'SONION', 'is_active' => true],
            ['code' => 'TDK', 'name' => 'TDK', 'is_active' => true],
            ['code' => 'UTA', 'name' => 'UTAC', 'is_active' => true],
            ['code' => 'VAR', 'name' => 'VAREX IMAGING', 'is_active' => true],
            ['code' => 'VIS', 'name' => 'VISHAY', 'is_active' => true],
            ['code' => 'VRT', 'name' => 'VRTS', 'is_active' => true],
            ['code' => 'WEJ', 'name' => 'WESTERN DIGITAL JAPAN', 'is_active' => true],
            ['code' => 'WEC', 'name' => 'WESTERN DIGITAL CHINA', 'is_active' => true],
            ['code' => 'WET', 'name' => 'WESTERN DIGITAL THAILAND', 'is_active' => true],
            ['code' => 'WEU', 'name' => 'WESTERN DIGITAL USA', 'is_active' => true],
            ['code' => 'ZIL', 'name' => 'ZILOG', 'is_active' => true],
            ['code' => 'EPS', 'name' => 'EPSON', 'is_active' => true],
            ['code' => 'ROH', 'name' => 'ROHM', 'is_active' => true],
            ['code' => 'WES', 'name' => 'WESTERN DIGITAL', 'is_active' => true],
            ['code' => 'LIE', 'name' => 'LITTELFUSE GERMANY', 'is_active' => true],
            ['code' => 'ONE', 'name' => 'ONSEMI CEBU', 'is_active' => true],
            ['code' => 'YAS', 'name' => 'YASHIRO SHOKAI CO. LTD', 'is_active' => true],
            ['code' => 'SPI', 'name' => 'SPI SmartProbe, Inc', 'is_active' => true],
            ['code' => 'All', 'name' => 'Allegro MicroSystems, LLC', 'is_active' => true],
            ['code' => 'PRO', 'name' => 'PROTEK', 'is_active' => true],
            ['code' => 'GPS', 'name' => 'GPSynergia', 'is_active' => true],
            ['code' => 'LIL', 'name' => 'LITTELFUSE PHILS.', 'is_active' => true],
            ['code' => 'TON', 'name' => 'TONG HSING', 'is_active' => true],
        ];

        foreach ($customers as $customer) {
            \App\Models\Customer::create($customer);
        }
    }
}
