<?php

declare(strict_types=1);

use KaliTools\KaliTools\KaliTools;

it('resolves the singleton', function () {
    expect(app(KaliTools::class))->toBeInstanceOf(KaliTools::class);
});

it('returns the same instance from the container', function () {
    expect(app(KaliTools::class))->toBe(app(KaliTools::class));
});

it('merges the package config', function () {
    expect(config('kali-tools.placeholder'))->toBe('default');
});

it('loads the package translations', function () {
    expect(trans('kali-tools::messages.placeholder'))->toBe('KaliTools placeholder translation.');
});

it('registers the artisan command', function () {
    $this->artisan('kali-tools:placeholder')
        ->expectsOutputToContain('KaliTools placeholder command executed.')
        ->assertSuccessful();
});
