<?php

use App\Models\Module;
use App\Models\News;

test('can create a module', function () {
    $news1 = News::factory()->create();
    $news2 = News::factory()->create();

    $configuration = [
        'model' => News::class,
        'ids' => [$news1->ID, $news2->ID],
    ];

    $response = $this->post(route('modules.store'), ['configuration' => $configuration]);
    $response->assertCreated();

    $module = Module::find($response->json('id'));
    expect(json_decode($module->configuration, true))->toBe($configuration);
});

test('can save a list', function () {
    $module = Module::factory()->create();
    $order = [3, 1, 2];

    $this->post(route('modules.saveList', $module), ['order' => $order])
         ->assertOk();

    $this->assertDatabaseHas('modules', [
        'id' => $module->id,
        'configuration' => json_encode(array_merge($module->configuration, ['ids' => $order])),
    ]);
});
