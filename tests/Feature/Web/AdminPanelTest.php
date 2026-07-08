<?php

it('serves the admin SPA shell at /admin', function () {
    $this->get('/admin')
        ->assertStatus(200)
        ->assertHeader('content-type', 'text/html; charset=UTF-8');
});

it('serves the admin SPA shell for any nested /admin path', function () {
    $this->get('/admin/content/news/overview')
        ->assertStatus(200)
        ->assertHeader('content-type', 'text/html; charset=UTF-8');
});
