<?php

/**
 * Minimal WordPress function stubs for testing.
 */
if (! function_exists('add_action')) {
    function add_action(): void {}
}

if (! function_exists('wp_parse_url')) {
    function wp_parse_url(string $url, int $component = -1): mixed
    {
        return parse_url($url, $component);
    }
}

if (! function_exists('site_url')) {
    function site_url(string $path = ''): string
    {
        global $_test_site_url;

        return rtrim($_test_site_url ?? 'http://example.test', '/').$path;
    }
}

if (! function_exists('apply_filters')) {
    function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
    {
        global $_test_filter_overrides;

        return $_test_filter_overrides[$hook] ?? $value;
    }
}

if (! function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        return true;
    }
}

if (! function_exists('maybe_unserialize')) {
    function maybe_unserialize(mixed $data): mixed
    {
        return is_string($data) ? @unserialize($data, ['allowed_classes' => false]) ?: $data : $data;
    }
}

if (! function_exists('is_multisite')) {
    function is_multisite(): bool
    {
        return false;
    }
}

if (! function_exists('is_subdomain_install')) {
    function is_subdomain_install(): bool
    {
        return false;
    }
}

if (! function_exists('sanitize_html_class')) {
    function sanitize_html_class(string $class): string
    {
        return $class;
    }
}

define('ENVIRONMENTS', [
    'development' => 'http://example.test/wp',
    'staging' => 'https://staging.example.com',
    'production' => 'https://example.com',
]);

define('WP_ENV', 'development');

require_once __DIR__.'/Support/FakeAdminBar.php';
require_once __DIR__.'/../wp-stage-switcher.php';
