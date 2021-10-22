<?php
/*
Plugin Name:  Stage Switcher
Plugin URI:   https://roots.io/plugins/stage-switcher/
Description:  A WordPress plugin that allows you to switch between different environments from the admin bar.
Version:      2.1.1
Author:       Roots
Author URI:   https://roots.io/
License:      MIT License
*/

namespace Roots\StageSwitcher;

/**
 * Require Composer autoloader if installed on it's own
 */
if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
  require_once $composer;
}

/**
 * Add stage/environment switcher to admin bar
 * Inspired by http://37signals.com/svn/posts/3535-beyond-the-default-rails-environments
 *
 * ENVIRONMENTS constant must be an array of 'environment' => 'url' elements:
 *
 *   $envs = [
 *    'development' => 'http://example.dev',
 *    'staging'     => 'http://example-staging.com',
 *    'production'  => 'http://example.com'
 *   ];
 *
 *   define( 'ENVIRONMENTS', $envs );
 *
 * If you're using PHP 5.6 or older you must serialize $envs first:
 *
 *   define('ENVIRONMENTS', serialize($envs));
 *
 * WP_ENV must be defined as the current environment.
 */
class StageSwitcher {
  public function __construct() {
    add_action('admin_bar_menu', [$this, 'admin_bar_stage_switcher']);
    add_action('wp_before_admin_bar_render', [$this, 'admin_css']);
  }

  public function admin_bar_stage_switcher($admin_bar) {
    if (!defined('ENVIRONMENTS') || !defined('WP_ENV') || !apply_filters('bedrock/stage_switcher_visibility', is_super_admin())) {
      return;
    }

    $stages = maybe_unserialize(ENVIRONMENTS);
    $current_stage = WP_ENV;

    foreach($stages as $stage => $url) {
      if ($stage === $current_stage) {
        continue;
      }

      if (is_multisite() && defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL && !is_main_site()) {
        $url = $this->multisite_url($url) . $_SERVER['REQUEST_URI'];
      } else {
        $url .= $_SERVER['REQUEST_URI'];
      }

      $admin_bar->add_menu([
        'id'     => 'environment',
        'parent' => 'top-secondary',
        'title'  => ucwords($current_stage),
        'href'   => '#',
        'meta'   => [
          'class' => 'environment-' . sanitize_html_class(strtolower($current_stage)),
        ],
      ]);

      $admin_bar->add_menu([
        'id'     => "stage_$stage",
        'parent' => 'environment',
        'title'  => ucwords($stage),
        'href'   => $url
      ]);
    }
  }

  public function admin_css() { ?>
    <style>
      #wp-admin-bar-environment > a:before {
        content: "\f177";
        top: 2px;
      }
    </style>
    <?php
  }

  private function multisite_url($url) {
    $stage_host = wp_parse_url($url, PHP_URL_HOST);
    $current_url = get_home_url(get_current_blog_id());
    $current_host = wp_parse_url($current_url, PHP_URL_HOST);
    $current_url= str_replace($current_host, $stage_host, $current_url);

    return rtrim($current_url, '/') . $_SERVER['REQUEST_URI'];
  }
}

new StageSwitcher;
