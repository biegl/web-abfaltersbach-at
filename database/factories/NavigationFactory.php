<?php

namespace Database\Factories;

use App\Models\Navigation;
use Illuminate\Database\Eloquent\Factories\Factory;

class NavigationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Navigation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'refID' => $this->faker->numberBetween(0, 10),
            'position' => $this->faker->numberBetween(0, 10),
            'level' => $this->faker->numberBetween(0, 1),
            'name' => $this->faker->text(10),
            'linkname' => $this->faker->slug(),
            'navianzeigen' => 'Ja',
        ];
    }
}
