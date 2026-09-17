<?php

namespace Modules\Booking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Booking\Models\Booking::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

