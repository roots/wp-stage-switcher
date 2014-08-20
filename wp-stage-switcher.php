<?php
/*
Plugin Name:  Stage Switcher
Plugin URI:   http://roots.io/plugins/stage-switcher/
Description:  A WordPress plugin that allows you to switch between different environments from the admin bar.
Version:      1.0.3
Author:       Ben Word
Author URI:   http://roots.io/
License:      MIT License
GitHub Plugin URI: https://github.com/roots/wp-stage-switcher
*/

namespace Roots\Bedrock;

/**
 * Add stage/environment switcher to admin bar
 * Inspired by http://37signals.com/svn/posts/3535-beyond-the-default-rails-environments
 *
 * ENVIRONMENTS constant must be a serialized array of 'environment' => 'url' elements:
 *
 *   $envs = array(
 *    'development' => 'http://example.dev',
 *    'staging'     => 'http://staging.example.com',
 *    'production'  => 'http://example.com'
 *   );
 *   define('ENVIRONMENTS', serialize($envs));
 *
 * WP_ENV must be defined as the current environment
 */
function admin_bar_stage_switcher($admin_bar) {
  if (defined('ENVIRONMENTS') && defined('WP_ENV') && apply_filters('bedrock_stage_switcher_visibility', is_super_admin())) {
    $stages = unserialize(ENVIRONMENTS);
    $current_stage = WP_ENV;
  } else {
    return;
  }

  $admin_bar->add_menu(array(
    'id'     => 'environment',
    'parent' => 'top-secondary',
    'title'  => ucwords($current_stage),
    'href'   => '#'
  ));

  $host_bits = explode('.', $_SERVER['HTTP_HOST']);
  $is_ms_subdomain = (is_multisite() && defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL && count($host_bits) > 2);

  foreach($stages as $stage => $url) {
    if ($stage === $current_stage) {
      continue;
    }

    if ($is_ms_subdomain) {
      $subdomain = $host_bits[0];
      $stage_host = parse_url($url, PHP_URL_HOST);
      $subd_stage_host = "$subdomain.$stage_host";
      $url = str_replace($stage_host, $subd_stage_host, $url);
      $url = rtrim($url, '/') . $_SERVER['REQUEST_URI'];
    } else {
      $stage_scheme = parse_url($stages[$current_stage], PHP_URL_SCHEME);
      $cur_stage_base = str_replace("$stage_scheme://", '', $stages[$current_stage]);
      $url .= str_replace($cur_stage_base, '', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    $admin_bar->add_menu(array(
      'id'     => "stage_$stage",
      'parent' => 'environment',
      'title'  => ucwords($stage),
      'href'   => $url
    ));
  }
}
add_action('admin_bar_menu', 'Roots\\Bedrock\\admin_bar_stage_switcher');

function admin_css() { ?>
  <style>
    #wp-admin-bar-environment > a:before {
      content: "\f177";
      top: 2px;
    }
  </style>
<?php }
add_action('wp_before_admin_bar_render', 'Roots\\Bedrock\\admin_css');
