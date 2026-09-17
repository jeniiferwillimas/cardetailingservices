<?php

namespace Modules\Content\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Content\Models\NavigationLink;

class NavigationLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = NavigationLink::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
