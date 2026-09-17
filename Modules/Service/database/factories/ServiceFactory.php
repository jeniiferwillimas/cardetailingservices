<?php

namespace Modules\Service\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Service\Models\Service::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

