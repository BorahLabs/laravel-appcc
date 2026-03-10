<?php

use AppccDigital\LaravelAppcc\AppccManager;

it('registers the appcc manager as a singleton', function () {
    $manager = app(AppccManager::class);

    expect($manager)->toBeInstanceOf(AppccManager::class);
    expect(app(AppccManager::class))->toBe($manager);
});

it('merges the config', function () {
    expect(config('appcc.url'))->toBe('https://test.appcc.digital');
    expect(config('appcc.token'))->toBe('test-token');
    expect(config('appcc.timeout'))->toBe(10);
});
