<?php

it('lists activities for an admin', function () {
    asAdmin();

    $this->getJson('api/activities')->assertStatus(200);
});
