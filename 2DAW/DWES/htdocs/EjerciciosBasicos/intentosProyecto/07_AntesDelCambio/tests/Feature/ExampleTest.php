<?php

test('the application returns a successful response', function () {
    $response = $this->getInstance('/');

    $response->assertStatus(200);
});
