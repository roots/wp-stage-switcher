# WordPress Stage Switcher
[![Packagist](https://img.shields.io/packagist/v/roots/wp-stage-switcher.svg?style=flat-square)](https://packagist.org/packages/roots/wp-stage-switcher)
[![Packagist Downloads](https://img.shields.io/packagist/dt/roots/wp-stage-switcher.svg?style=flat-square)](https://packagist.org/packages/roots/wp-stage-switcher)

A WordPress plugin that allows you to switch between different environments from the admin bar.

![WordPress Stage Switcher](https://cdn.roots.io/app/uploads/plugin-stage-switcher.png)

## Requirements

You'll need to have `ENVIRONMENTS` and `WP_ENV` defined in your WordPress config.

The `ENVIRONMENTS` constant must be an array of `'environment' => 'url'` elements:

```php
$envs = [
  'development' => 'http://example.dev',
  'staging'     => 'http://staging.example.com',
  'production'  => 'http://example.com'
];
Config::define('ENVIRONMENTS', $envs);
```

`WP_ENV` must be defined as the current environment:

```php
Config::define('WP_ENV', 'development');
```

If you use [Bedrock](https://github.com/roots/bedrock), `WP_ENV` is already defined in the config.

## Installation

This plugin must be installed via Composer. Add wp-stage-switcher to your project's dependencies:

```sh
composer require roots/wp-stage-switcher
```

## Filters

### `bedrock/stage_switcher_colors`

Customize the background colors for each environment in the admin bar menu. Returns an array of `'environment' => 'color'` pairs.

Default colors:
```php
[
  'development' => 'firebrick',
  'staging'     => 'chocolate',
  'production'  => 'transparent',
]
```

Example usage:
```php
add_filter('bedrock/stage_switcher_colors', function ($colors) {
  return [
    'development' => '#dc2626',
    'staging'     => '#ea580c',
    'production'  => '#10b981',
  ];
});
```

### `bedrock/stage_switcher_visibility`

Control who can see the stage switcher in the admin bar. Defaults to `is_super_admin()`.

Example usage:
```php
add_filter('bedrock/stage_switcher_visibility', function ($visible) {
  return current_user_can('manage_options');
});
```

## Support

Use the [Roots Discourse](http://discourse.roots.io/) to ask questions and get support.
