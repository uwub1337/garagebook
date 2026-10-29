<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', 'demo@garagebook.test')->delete();

        $user = User::create([
            'name' => '示範車主',
            'email' => 'demo@garagebook.test',
            'password' => 'password123',
        ]);

        $car1 = $user->cars()->create([
            'name' => '小白',
            'make' => 'Toyota',
            'model' => 'Corolla Cross',
            'year' => 2022,
            'plate' => 'ABC-1234',
            'current_mileage' => 40300,
            'fuel_type' => 'gasoline',
        ]);

        $car1->maintenanceRecords()->createMany([
            ['item' => '機油更換', 'date' => '2026-04-12', 'mileage' => 32000, 'cost' => 2400, 'next_due_date' => '2026-10-12', 'next_due_mileage' => 37000, 'notes' => '5W-30 全合成機油'],
            ['item' => '四輪定位', 'date' => '2026-02-18', 'mileage' => 30000, 'cost' => 1500, 'next_due_date' => '2027-02-18'],
            ['item' => '煞車來令片', 'date' => '2026-05-06', 'mileage' => 33100, 'cost' => 3200, 'next_due_date' => '2026-11-06', 'notes' => '前輪來令片'],
            ['item' => '冷氣濾網', 'date' => '2026-07-15', 'mileage' => 37100, 'cost' => 900, 'next_due_date' => '2027-07-15'],
            ['item' => '輪胎更換', 'date' => '2026-09-20', 'mileage' => 39800, 'cost' => 12800, 'next_due_mileage' => 60000, 'notes' => '四條 Michelin Primacy'],
        ]);

        $car1->fuelRecords()->createMany([
            ['date' => '2026-01-10', 'mileage' => 33800, 'liters' => 38.2, 'cost' => 1156],
            ['date' => '2026-02-08', 'mileage' => 34350, 'liters' => 36.5, 'cost' => 1095],
            ['date' => '2026-03-09', 'mileage' => 34880, 'liters' => 37.1, 'cost' => 1141],
            ['date' => '2026-04-12', 'mileage' => 35500, 'liters' => 39.4, 'cost' => 1222],
            ['date' => '2026-05-11', 'mileage' => 36220, 'liters' => 40.2, 'cost' => 1290],
            ['date' => '2026-06-10', 'mileage' => 36980, 'liters' => 39.8, 'cost' => 1288],
            ['date' => '2026-07-09', 'mileage' => 37760, 'liters' => 41.1, 'cost' => 1339],
            ['date' => '2026-08-08', 'mileage' => 38600, 'liters' => 40.5, 'cost' => 1326],
            ['date' => '2026-09-07', 'mileage' => 39440, 'liters' => 39.9, 'cost' => 1310],
            ['date' => '2026-10-06', 'mileage' => 40300, 'liters' => 41.3, 'cost' => 1355],
        ]);

        $car2 = $user->cars()->create([
            'name' => '黑武士',
            'make' => 'Ford',
            'model' => 'Ranger',
            'year' => 2019,
            'plate' => 'XYZ-5678',
            'current_mileage' => 96500,
            'fuel_type' => 'diesel',
        ]);

        $car2->maintenanceRecords()->createMany([
            ['item' => '柴油濾芯', 'date' => '2026-09-30', 'mileage' => 95900, 'cost' => 1800, 'next_due_mileage' => 115900],
            ['item' => '變速箱油', 'date' => '2026-03-15', 'mileage' => 88000, 'cost' => 5600, 'next_due_date' => '2027-03-15'],
            ['item' => '四輪定位', 'date' => '2025-12-05', 'mileage' => 80200, 'cost' => 1500, 'next_due_date' => '2026-12-05'],
            ['item' => '引擎空氣濾芯', 'date' => '2026-10-02', 'mileage' => 96000, 'cost' => 1100, 'next_due_date' => '2026-10-30'],
            ['item' => '煞車油', 'date' => '2026-06-11', 'mileage' => 91600, 'cost' => 1200, 'next_due_date' => '2028-06-11'],
        ]);

        $car2->fuelRecords()->createMany([
            ['date' => '2026-05-05', 'mileage' => 90000, 'liters' => 52.3, 'cost' => 1650],
            ['date' => '2026-06-14', 'mileage' => 91600, 'liters' => 50.8, 'cost' => 1610],
            ['date' => '2026-07-20', 'mileage' => 93200, 'liters' => 51.5, 'cost' => 1680],
            ['date' => '2026-09-01', 'mileage' => 94900, 'liters' => 53.0, 'cost' => 1740],
            ['date' => '2026-10-10', 'mileage' => 96500, 'liters' => 51.2, 'cost' => 1690],
        ]);
    }
}
