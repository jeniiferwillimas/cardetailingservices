<?php

namespace Modules\Content\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Content\Models\GalleryImage;
use Modules\Content\Models\NavigationLink;

class ContentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = [
            ['label' => 'Home', 'href' => '/', 'sort_order' => 1],
            ['label' => 'About Us', 'href' => '/about', 'sort_order' => 2],
            ['label' => 'Services', 'href' => '/cardetailingservices', 'sort_order' => 3],
            ['label' => 'Gift Certificates', 'href' => '/gift-certificates', 'sort_order' => 4],
            ['label' => 'Gallery', 'href' => '/gallery', 'sort_order' => 5],
        ];

        foreach ($links as $link) {
            NavigationLink::updateOrCreate(['label' => $link['label']], $link);
        }

        $images = [
            [
                'image_url' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?q=80&w=1200&auto=format&fit=crop',
                'alt_text' => 'Detailer applying wax to a car panel',
                'sort_order' => 1,
            ],
            [
                'image_url' => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?q=80&w=1200&auto=format&fit=crop',
                'alt_text' => 'Microfiber cloth polishing car surface',
                'sort_order' => 2,
            ],
            [
                'image_url' => 'https://images.unsplash.com/photo-1600661653561-629509216228?q=80&w=1200&auto=format&fit=crop',
                'alt_text' => 'Car being hand washed',
                'sort_order' => 3,
            ],
            [
                'image_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=1200&auto=format&fit=crop',
                'alt_text' => 'Modern car interior dashboard',
                'sort_order' => 4,
            ],
            [
                'image_url' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?q=80&w=1200&auto=format&fit=crop',
                'alt_text' => 'Close-up of a detailed headlight',
                'sort_order' => 5,
            ],
            [
                'image_url' => 'https://images.unsplash.com/photo-1607860108855-64acf2078ed9?q=80&w=1200&auto=format&fit=crop',
                'alt_text' => 'Detailer polishing headlight',
                'sort_order' => 6,
            ],
        ];

        foreach ($images as $image) {
            GalleryImage::updateOrCreate(['image_url' => $image['image_url']], $image);
        }
    }
}
