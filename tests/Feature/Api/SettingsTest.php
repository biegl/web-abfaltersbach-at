<?php

beforeEach(fn () => asUser());

it('shows the general settings', function () {
    $this->getJson('api/settings')
        ->assertStatus(200)
        ->assertJsonStructure(['isProxyCardFeatureAvailable']);
});

it('updates the general settings', function () {
    $this->putJson('api/settings', ['isProxyCardFeatureAvailable' => false])
        ->assertStatus(200)
        ->assertJson(['isProxyCardFeatureAvailable' => false]);
});

it('validates the general settings on update', function () {
    $this->putJson('api/settings', ['isProxyCardFeatureAvailable' => 'not-a-bool'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['is_proxy_card_feature_available']);
});
