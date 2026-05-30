<?php

test('example feature test', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
