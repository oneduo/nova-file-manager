<?php

declare(strict_types=1);

use function Pest\Laravel\getJson;

it('can retrieve available disks', function () {
    getJson(uri: route('nova-file-manager.disks.available'))
        ->assertOk()
        ->assertJson([
            'public',
        ]);
});
