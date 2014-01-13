<?php
/*
Plugin Name:  Stage Switcher
Plugin URI:   http://roots.io/plugins/stage-switcher/
Description:  A WordPress plugin that allows you to switch between different environments from the admin bar.
Version:      1.0.0
Author:       Ben Word
Author URI:   http://roots.io/
License:      MIT License
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
  if (defined('ENVIRONMENTS') && defined('WP_ENV')) {
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

  foreach($stages as $stage => $url) {
    if ($stage === $current_stage) {
      continue;
    }

    $url .= $_SERVER['REQUEST_URI'];

    $admin_bar->add_menu(array(
      'id'     => $stage,
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
