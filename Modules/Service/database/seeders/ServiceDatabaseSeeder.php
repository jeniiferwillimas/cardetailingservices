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
        $packages = [
            [
                'slug' => 'interior-deluxe',
                'name' => 'Interior Deluxe',
                'description' => "Restore your car's interior to its former glory with the Interior Deluxe package. This service focuses on a thorough cleaning and rejuvenation of your car's inside. We'll tackle dirt and grime in every crevice, including cup holders, side panels, and seats. Leather cleaning and UV protection for vinyl surfaces ensure a clean and conditioned interior. This package also includes odor removal to leave your cabin freshened and refreshed.",
                'price' => 199.99,
                'duration_min' => 90,
                'image_url' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'slug' => 'ultimate-package',
                'name' => 'Ultimate Package',
                'description' => 'The Ultimate Package delivers a comprehensive deep clean and protection for both the interior and exterior of your vehicle. Our team hand washes, clays, and waxes the exterior while thoroughly vacuuming, shampooing, and conditioning every interior surface, leaving your car looking and feeling brand new inside and out.',
                'price' => 249.99,
                'duration_min' => 120,
                'image_url' => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'slug' => 'super-shine-package',
                'name' => 'Super Shine Package',
                'description' => "Take your car's shine to the next level with the Super Shine Package. Our detailers perform a premium hand wash, clay bar treatment to strip embedded contaminants, and a long-lasting wax finish that leaves your paint looking glossy, smooth, and protected against the elements.",
                'price' => 299.99,
                'duration_min' => 150,
                'image_url' => 'https://images.unsplash.com/photo-1600661653561-629509216228?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'slug' => 'showroom-detail',
                'name' => 'Showroom Detail',
                'description' => 'Experience the ultimate in car care with the Showroom Detail. This full-service package combines deep interior cleaning, exterior paint correction, and a premium finish to bring your vehicle back to showroom-quality condition, inside and out.',
                'price' => 399.99,
                'duration_min' => 180,
                'image_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'slug' => 'ceramic-package',
                'name' => '5 Year Ceramic Package',
                'description' => 'Invest in the ultimate protection for your car with the 5 Year Ceramic Package. Our professional-grade ceramic coating bonds to your paint to guard against UV damage, oxidation, and minor scratches, keeping your vehicle glossy and protected for years to come.',
                'price' => 999.99,
                'duration_min' => 240,
                'image_url' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?q=80&w=1200&auto=format&fit=crop',
            ],
        ];

        $addons = [
            [
                'slug' => 'engine-cleaning',
                'name' => 'Engine Cleaning',
                'description' => "Restore your engine's power and performance with our deep engine bay cleaning.",
                'price' => 100,
                'duration_min' => 30,
            ],
            [
                'slug' => 'headlight-restoration',
                'name' => 'Headlight Restoration',
                'description' => 'Dull and cloudy headlights can be a safety hazard — see clearly, drive safely.',
                'price' => 150,
                'duration_min' => 45,
            ],
            [
                'slug' => 'ozone-treatment-odor-removal',
                'name' => 'Ozone Treatment Odor Removal',
                'description' => 'Eliminate unwanted odors and banish stubborn smells like smoke and pet odor.',
                'price' => 100,
                'duration_min' => 30,
            ],
            [
                'slug' => 'excessive-pet-hair-extraction',
                'name' => 'Excessive Pet Hair Extraction',
                'description' => 'Struggling with pet hair? Our specialized cleaning gets a pet-free interior.',
                'price' => 150,
                'duration_min' => 45,
            ],
            [
                'slug' => 'steam-interior-cleaning',
                'name' => 'Steam Interior Cleaning',
                'description' => "Deep clean your car's interior — our steam cleaning process penetrates deep.",
                'price' => 100,
                'duration_min' => 45,
            ],
            [
                'slug' => 'pest-control-fumigation',
                'name' => 'Pest Control Fumigation',
                'description' => 'Protect your vehicle from pests and prevent costly damage and health risks.',
                'price' => 400,
                'duration_min' => 60,
            ],
        ];

        foreach ($packages as $package) {
            Service::updateOrCreate(
                ['slug' => $package['slug']],
                [...$package, 'type' => 'package']
            );
        }

        foreach ($addons as $addon) {
            Service::updateOrCreate(
                ['slug' => $addon['slug']],
                [...$addon, 'type' => 'addon']
            );
        }
    }
}
