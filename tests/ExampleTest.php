<?php

declare(strict_types=1);

test('confirm environment is set to testing', function () {
    expect(config('app.env'))->toBe('testing');
});
