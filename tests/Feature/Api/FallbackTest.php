<?php

it('returns 404 for an unknown api endpoint', function () {
    $this->getJson('api/does-not-exist')
        ->assertStatus(404)
        ->assertSee('Endpoint does not exist!');
});
