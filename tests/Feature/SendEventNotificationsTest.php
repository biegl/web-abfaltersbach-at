<?php

namespace Tests\Feature;

use App\Models\Event;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Telegram\Telegram;
use Tests\TestCase;

class SendEventNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set a fake Telegram channel for testing
        Config::set('services.telegram-bot-api.channel', 'test-channel');

        // Mock the Telegram service with proper response objects
        $this->mock(Telegram::class, function ($mock) {
            $response = new Response(200, [], json_encode(['ok' => true]));
            $mock->shouldReceive('sendMessage')->andReturn($response);
            $mock->shouldReceive('sendPhoto')->andReturn($response);
            $mock->shouldReceive('sendFile')->andReturn($response);
        });
    }

    /** @test */
    public function it_sends_notifications_for_todays_events()
    {
        // Create an event for today
        $event = Event::factory()->create([
            'date' => Carbon::today(),
            'text' => 'Test event content',
            'notification_sent_at' => null,
        ]);

        // Run the command
        $this->artisan('events:send-notifications')
            ->expectsOutput('Event notifications sent successfully.')
            ->assertExitCode(0);

        // Assert the event was marked as notified
        $this->assertNotNull($event->fresh()->notification_sent_at);
    }

    /** @test */
    public function it_sends_notifications_for_events_with_images()
    {
        // Create an event for today with an image
        $event = Event::factory()->create([
            'date' => Carbon::today(),
            'text' => 'Test event content with <img src="test.jpg"> image',
            'notification_sent_at' => null,
        ]);

        // Run the command
        $this->artisan('events:send-notifications')
            ->expectsOutput('Event notifications sent successfully.')
            ->assertExitCode(0);

        // Assert the event was marked as notified
        $this->assertNotNull($event->fresh()->notification_sent_at);
    }

    /** @test */
    public function it_skips_events_that_were_already_notified()
    {
        // Create an event that was already notified
        Event::factory()->create([
            'date' => Carbon::today(),
            'text' => 'Test event content',
            'notification_sent_at' => Carbon::now()->subHour(),
        ]);

        // Run the command
        $this->artisan('events:send-notifications')
            ->expectsOutput('No new events to notify for today.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_only_sends_notifications_for_todays_events()
    {
        // Create events for yesterday, today, and tomorrow
        Event::factory()->create([
            'date' => Carbon::yesterday(),
            'text' => 'Yesterday event',
            'notification_sent_at' => null,
        ]);

        Event::factory()->create([
            'date' => Carbon::today(),
            'text' => 'Today event',
            'notification_sent_at' => null,
        ]);

        Event::factory()->create([
            'date' => Carbon::tomorrow(),
            'text' => 'Tomorrow event',
            'notification_sent_at' => null,
        ]);

        // Run the command
        $this->artisan('events:send-notifications')
            ->expectsOutput('Event notifications sent successfully.')
            ->assertExitCode(0);

        // Assert only today's event was marked as notified
        $this->assertDatabaseHas('tbl_events', [
            'text' => 'Today event',
        ]);
        $this->assertNotNull(Event::where('text', 'Today event')->first()->notification_sent_at);

        // Assert other events were not notified
        $this->assertDatabaseHas('tbl_events', [
            'text' => 'Yesterday event',
            'notification_sent_at' => null,
        ]);

        $this->assertDatabaseHas('tbl_events', [
            'text' => 'Tomorrow event',
            'notification_sent_at' => null,
        ]);
    }
}
