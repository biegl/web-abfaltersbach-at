<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'date' => Carbon::today(),
            'text' => $this->faker->paragraph(),
            'notification_sent_at' => null,
        ];
    }

    /**
     * Indicate that the event has been notified.
     */
    public function notified()
    {
        return $this->state(function (array $attributes) {
            return [
                'notification_sent_at' => Carbon::now(),
            ];
        });
    }
}
