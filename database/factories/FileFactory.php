<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fakeFile = UploadedFile::fake()->create($this->faker->word().'.pdf', 1000);

        return [
            'navID' => $this->faker->numberBetween(0, 255),
            'position' => $this->faker->numberBetween(0, 10),
            'title' => $this->faker->sentence(4),
            'file' => '/uploads/test-files/'.$fakeFile->name,
        ];
    }
}
