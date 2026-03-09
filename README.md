# WordPress Stage Switcher
[![Packagist](https://img.shields.io/packagist/v/roots/wp-stage-switcher.svg?style=flat-square)](https://packagist.org/packages/roots/wp-stage-switcher)
[![Packagist Downloads](https://img.shields.io/packagist/dt/roots/wp-stage-switcher?label=downloads&colorB=2b3072&colorA=525ddc&style=flat-square)](https://packagist.org/packages/roots/wp-stage-switcher)
![Build Status](https://img.shields.io/github/actions/workflow/status/roots/wp-stage-switcher/main.yml?branch=master&style=flat-square)
[![Follow Roots](https://img.shields.io/badge/follow%20@rootswp-1da1f2?logo=twitter&logoColor=ffffff&message=&style=flat-square)](https://twitter.com/rootswp)
[![Sponsor Roots](https://img.shields.io/badge/sponsor%20roots-525ddc?logo=github&style=flat-square&logoColor=ffffff&message=)](https://github.com/sponsors/roots)

A WordPress plugin that allows you to switch between different environments from the admin bar.

![WordPress Stage Switcher](https://cdn.roots.io/app/uploads/plugin-stage-switcher.png)

## Support us

We're dedicated to pushing modern WordPress development forward through our open source projects, and we need your support to keep building. You can support our work by purchasing [Radicle](https://roots.io/radicle/), our recommended WordPress stack, or by [sponsoring us on GitHub](https://github.com/sponsors/roots). Every contribution directly helps us create better tools for the WordPress ecosystem.

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

## Community

Keep track of development and community news.

- Join us on Discord by [sponsoring us on GitHub](https://github.com/sponsors/roots)
- Join us on [Roots Discourse](https://discourse.roots.io/)
- Follow [@rootswp on Twitter](https://twitter.com/rootswp)
- Follow the [Roots Blog](https://roots.io/blog/)
- Subscribe to the [Roots Newsletter](https://roots.io/subscribe/)
