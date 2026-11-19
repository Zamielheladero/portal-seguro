<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

// El inicio ahora consulta los productos destacados.
uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
