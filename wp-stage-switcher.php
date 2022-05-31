<?php
/*
Plugin Name:  Stage Switcher
Plugin URI:   https://roots.io/plugins/stage-switcher/
Description:  A WordPress plugin that allows you to switch between different environments from the admin bar.
Version:      2.2.0
Author:       Roots
Author URI:   https://roots.io/
License:      MIT License
*/

namespace Roots\StageSwitcher;

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
 *
 * For multisite subdomain installations, the host portion of the specified
 * environment URL will be treated as a suffix in constructing a matching blog
 * URL in that environment.
 */
class StageSwitcher {
  private $stages;

  public function __construct() {
    add_action('admin_bar_menu', [$this, 'admin_bar_stage_switcher']);
    add_action('wp_before_admin_bar_render', [$this, 'admin_css']);
  }

  public function admin_bar_stage_switcher($admin_bar) {
    if (!defined('ENVIRONMENTS') || !defined('WP_ENV') || !apply_filters('bedrock/stage_switcher_visibility', is_super_admin())) {
      return;
    }

    $this->stages = maybe_unserialize(ENVIRONMENTS);
    $subdomain_multisite = is_multisite() && is_subdomain_install();

    $admin_bar->add_menu([
      'id'     => 'environment',
      'parent' => 'top-secondary',
      'title'  => ucwords(WP_ENV),
      'href'   => '#',
      'meta'   => [
        'class' => 'environment-' . sanitize_html_class(strtolower(WP_ENV)),
      ],
    ]);

    foreach ($this->stages as $stage => $url) {
      if ($stage === WP_ENV) {
        continue;
      }

      if ($subdomain_multisite) {
        $url = $this->multisite_url($url);
      }

      $url = apply_filters('bedrock/stage_switcher_url', rtrim($url, '/') . $_SERVER['REQUEST_URI'], $url, $stage);

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
      #wpadminbar #wp-admin-bar-environment > .ab-item:before {
        content: "\f177";
        top: 2px;
      }
    </style>
    <?php
  }

  private function multisite_url($url) {
    // Normalize URL to ensure it can be successfully parsed
    $url = esc_url($url);

    $current_host = wp_parse_url(get_home_url(get_current_blog_id()), PHP_URL_HOST);
    $current_stage_host_suffix = wp_parse_url($this->stages[WP_ENV], PHP_URL_HOST);
    $target_stage_host_suffix = wp_parse_url($url, PHP_URL_HOST);

    // Using preg_replace to anchor to the end of the host string
    $target_host = preg_replace('/' . preg_quote($current_stage_host_suffix) . '$/', $target_stage_host_suffix, $current_host);

    // Use the stage URL as the base for replacement to keep scheme/port
    return str_replace($target_stage_host_suffix, $target_host, $url);
  }
}

new StageSwitcher;
