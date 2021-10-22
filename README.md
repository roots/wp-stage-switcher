# WordPress Stage Switcher
[![Packagist](https://img.shields.io/packagist/v/roots/wp-stage-switcher.svg?style=flat-square)](https://packagist.org/packages/roots/wp-stage-switcher)
[![Packagist Downloads](https://img.shields.io/packagist/dt/roots/wp-stage-switcher.svg?style=flat-square)](https://packagist.org/packages/roots/wp-stage-switcher)

A WordPress plugin that allows you to switch between different environments from the admin bar.

![WordPress Stage Switcher](https://cdn.roots.io/app/uploads/plugin-stage-switcher.png)

## Requirements

You'll need to have `ENVIRONMENTS` and `WP_ENV` defined in your WordPress config.

The `ENVIRONMENTS` constant must be a serialized array of `'environment' => 'url'` elements:

```php
$envs = [
  'development' => 'http://example.dev',
  'staging'     => 'http://staging.example.com',
  'production'  => 'http://example.com'
];
define('ENVIRONMENTS', serialize($envs));
```

Note: the `serialize()` call is not needed on PHP 7.0 or newer.

`WP_ENV` must be defined as the current environment:

```php
define('WP_ENV', 'development');
```

If you use [Bedrock](https://github.com/roots/bedrock), `WP_ENV` is already defined in the config.

## Installation

This plugin must be installed via Composer. Add wp-stage-switcher to your project's dependencies:

```sh
composer require roots/wp-stage-switcher
```

Or manually add it to your `composer.json`:

```json
"require": {
  "php": ">=7.1",
  "roots/wordpress": "5.1.1",
  "roots/wp-stage-switcher": "~2.1"
}
```

## Support

Use the [Roots Discourse](http://discourse.roots.io/) to ask questions and get support.
