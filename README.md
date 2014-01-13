# WordPress Stage Switcher

A WordPress plugin that allows you to switch between different environments from the admin bar.

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

If you're using Composer to manage WordPress, open up your `composer.json` and add wp-stage-switcher to your project's dependencies:

```json
"require": {
  "php": ">=5.3.0",
  "wordpress": "3.8",
  "roots/wp-stage-switcher": "1.0.0"
}
```

## Support

Use the [Roots Discourse](http://discourse.roots.io/) to ask questions and get support.
