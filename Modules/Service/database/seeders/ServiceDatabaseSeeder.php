<?php

namespace Modules\Service\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Service\Models\Service;

class ServiceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Service::count() > 0) {
            return;
        }

        $services = [
            ['name' => 'Exterior Wash & Wax', 'description' => 'Hand wash, clay bar, and premium wax finish.', 'price' => 45.00, 'duration_min' => 60],
            ['name' => 'Interior Deep Clean', 'description' => 'Vacuum, shampoo seats/carpets, dashboard and console detail.', 'price' => 65.00, 'duration_min' => 90],
            ['name' => 'Full Detail Package', 'description' => 'Complete interior and exterior detailing.', 'price' => 150.00, 'duration_min' => 180],
            ['name' => 'Ceramic Coating', 'description' => 'Long-lasting paint protection with ceramic coating.', 'price' => 350.00, 'duration_min' => 240],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
