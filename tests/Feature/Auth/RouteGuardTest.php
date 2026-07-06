<?php

// Every auth:sanctum route returns 401 when unauthenticated.
it('blocks unauthenticated access', function (string $method, string $uri) {
    $this->json($method, $uri)->assertStatus(401);
})->with([
    ['get', 'api/news'],
    ['post', 'api/news'],
    ['get', 'api/news/1'],
    ['put', 'api/news/1'],
    ['delete', 'api/news/1'],
    ['post', 'api/news/1/attach'],
    ['get', 'api/files'],
    ['post', 'api/files'],
    ['get', 'api/files/1'],
    ['put', 'api/files/1'],
    ['delete', 'api/files/1'],
    ['get', 'api/events'],
    ['post', 'api/events'],
    ['get', 'api/events/1'],
    ['put', 'api/events/1'],
    ['delete', 'api/events/1'],
    ['post', 'api/events/1/attach'],
    ['get', 'api/pages'],
    ['post', 'api/pages'],
    ['get', 'api/pages/1'],
    ['put', 'api/pages/1'],
    ['delete', 'api/pages/1'],
    ['post', 'api/pages/1/attach'],
    ['get', 'api/navigation'],
    ['get', 'api/persons'],
    ['post', 'api/persons'],
    ['get', 'api/persons/1'],
    ['put', 'api/persons/1'],
    ['delete', 'api/persons/1'],
    ['get', 'api/persons/list/1'],
    ['post', 'api/persons/list/1'],
    ['post', 'api/persons/1/attach'],
    ['post', 'api/persons/1/delete/1'],
    ['get', 'api/analytics'],
    ['get', 'api/settings'],
    ['put', 'api/settings'],
    ['get', 'api/users'],
    ['post', 'api/users'],
    ['get', 'api/users/1'],
    ['put', 'api/users/1'],
    ['delete', 'api/users/1'],
    ['post', 'api/users/1/revoke'],
    ['get', 'api/activities'],
]);

// Every isAdmin route returns 401 for an authenticated non-admin.
it('blocks non-admins from admin routes', function (string $method, string $uri) {
    asUser();
    $this->json($method, $uri)->assertStatus(401);
})->with([
    ['get', 'api/users'],
    ['post', 'api/users'],
    ['get', 'api/users/1'],
    ['put', 'api/users/1'],
    ['delete', 'api/users/1'],
    ['post', 'api/users/1/revoke'],
    ['get', 'api/activities'],
]);
