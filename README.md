# WordPress Stage Switcher

A WordPress plugin that allows you to switch between different environments from the admin bar.

![WordPress Stage Switcher](http://roots.io/media/wordpress-stage-switcher.png)

## Requirements

You'll need to have `ENVIRONMENTS` and `WP_ENV` defined in your WordPress config.

The `ENVIRONMENTS` constant must be a serialized array of `'environment' => 'url'` elements:

```php
$envs = array(
  'development' => 'http://example.dev',
  'staging'     => 'http://staging.example.com',
  'production'  => 'http://example.com'
);
define('ENVIRONMENTS', serialize($envs));
```

`WP_ENV` must be defined as the current environment:

```php
define('WP_ENV', 'development');
```

If you use [Bedrock](https://github.com/roots/bedrock), `WP_ENV` is already defined in the config.

## Installation

If you're using Composer to manage WordPress, add wp-stage-switcher to your project's dependencies. Run:

```sh
composer require roots/wp-stage-switcher 1.0.3
```

Or manually add it to your `composer.json`:

```json
"require": {
  "php": ">=5.3.0",
  "wordpress": "3.9.2",
  "roots/wp-stage-switcher": "1.0.3"
}
```

## Support

Use the [Roots Discourse](http://discourse.roots.io/) to ask questions and get support.
