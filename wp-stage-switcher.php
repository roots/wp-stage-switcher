<?php
/*
Plugin Name:  Stage Switcher
Plugin URI:   https://roots.io/plugins/stage-switcher/
Description:  A WordPress plugin that allows you to switch between different environments from the admin bar.
Version:      2.0.0
Author:       Roots
Author URI:   https://roots.io/
License:      MIT License
*/

namespace Roots\StageSwitcher;

use Purl\Url;

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
 * ENVIRONMENTS constant must be a serialized array of 'environment' => 'url' elements:
 *
 *   $envs = [
 *    'development' => 'http://example.dev',
 *    'staging'     => 'http://example-staging.com',
 *    'production'  => 'http://example.com'
 *   ];
 *
 *   define('ENVIRONMENTS', serialize($envs));
 *
 * WP_ENV must be defined as the current environment
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

    $stages = unserialize(ENVIRONMENTS);
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
    $stage_url = new Url($url);
    $current_site = new Url(get_home_url(get_current_blog_id()));
    $current_site->host = str_replace($current_site->registerableDomain, $stage_url->registerableDomain, $current_site->host);

    return rtrim($current_site->getUrl(), '/') . $_SERVER['REQUEST_URI'];
  }
}

new StageSwitcher;
