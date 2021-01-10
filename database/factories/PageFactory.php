<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'seitentitel' => $this->faker->text(20),
            'keywords' => $this->faker->text(10),
            'template' => $this->faker->text(50),
            'template_name' => $this->faker->text(45),
            'inhalt' => $this->faker->sentence(50),
            'description' => $this->faker->sentence(20),
        ];
    }
}
