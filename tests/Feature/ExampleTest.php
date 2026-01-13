<?php

it('returns a successful response', function () {
    $response = $this->get('/health');

    $response->assertStatus(200);
});
