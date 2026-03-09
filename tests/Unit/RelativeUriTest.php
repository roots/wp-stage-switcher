<?php

use Roots\StageSwitcher\StageSwitcher;

afterEach(function () {
    global $_test_site_url, $_test_filter_overrides;
    $_test_site_url = null;
    $_test_filter_overrides = [];
    unset($_SERVER['REQUEST_URI']);
});

function callRelativeUri(StageSwitcher $instance, string $request_uri): string
{
    $method = new ReflectionMethod($instance, 'relative_uri');

    return $method->invoke($instance, $request_uri);
}

function switcher(string $siteUrl = 'http://example.test/wp', array $stages = []): StageSwitcher
{
    global $_test_site_url;
    $_test_site_url = $siteUrl;

    $instance = new StageSwitcher;

    $reflection = new ReflectionProperty(StageSwitcher::class, 'stages');
    $reflection->setValue($instance, $stages);

    return $instance;
}

it('strips subfolder prefix from request path', function () {
    expect(callRelativeUri(switcher(), '/wp/about'))->toBe('/about');
});

it('strips exact base path to root', function () {
    expect(callRelativeUri(switcher(), '/wp'))->toBe('/');
});

it('does not strip partial segment matches', function () {
    expect(callRelativeUri(switcher(), '/wp-json/wp/v2/posts'))->toBe('/wp-json/wp/v2/posts');
});

it('preserves query strings', function () {
    expect(callRelativeUri(switcher(), '/wp/about?foo=bar'))->toBe('/about?foo=bar');
});

it('returns request uri unchanged when no prefix matches', function () {
    expect(callRelativeUri(switcher('http://example.test'), '/about'))->toBe('/about');
});

it('falls back to ENVIRONMENTS config path', function () {
    $switcher = switcher(
        'http://example.test',
        ['development' => 'http://example.test/app'],
    );

    expect(callRelativeUri($switcher, '/app/about'))->toBe('/about');
});

it('keeps root path as root', function () {
    expect(callRelativeUri(switcher(), '/'))->toBe('/');
});

it('assembles correct target URL with query string', function () {
    $_SERVER['REQUEST_URI'] = '/wp/about?foo=bar';

    $adminBar = new WP_Admin_Bar;
    $switcher = new StageSwitcher;
    $switcher->admin_bar_stage_switcher($adminBar);

    expect($adminBar->menus['stage_staging']['href'])->toBe('https://staging.example.com/about?foo=bar');
    expect($adminBar->menus['stage_production']['href'])->toBe('https://example.com/about?foo=bar');
});
