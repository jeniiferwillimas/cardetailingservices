<?php

namespace Modules\Content\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Content\Models\GalleryImage;

class GalleryImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = GalleryImage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
