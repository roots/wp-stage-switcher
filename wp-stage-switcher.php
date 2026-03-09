<?php
/*
Plugin Name:  Stage Switcher
Plugin URI:   https://roots.io/plugins/stage-switcher/
Description:  A WordPress plugin that allows you to switch between different environments from the admin bar.
Version:      2.3.0
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
 *   Config::define('ENVIRONMENTS', $envs);
 *
 * WP_ENV must be defined as the current environment.
 *
 * For multisite subdomain installations, the host portion of the specified
 * environment URL will be treated as a suffix in constructing a matching blog
 * URL in that environment.
 */
class StageSwitcher
{
    private array $stages = [];

    public function __construct()
    {
        add_action('admin_bar_menu', [$this, 'admin_bar_stage_switcher']);
        add_action('wp_before_admin_bar_render', [$this, 'admin_css']);
    }

    public function admin_bar_stage_switcher(\WP_Admin_Bar $admin_bar): void
    {
        if (! defined('ENVIRONMENTS') || ! defined('WP_ENV') || ! apply_filters('bedrock/stage_switcher_visibility', is_super_admin())) {
            return;
        }

        $stages = maybe_unserialize(ENVIRONMENTS);
        $this->stages = is_array($stages) ? $stages : [];
        $subdomain_multisite = is_multisite() && is_subdomain_install();
        $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
        $request_uri = $this->relative_uri($request_uri);

        $admin_bar->add_menu([
            'id' => 'environment',
            'parent' => 'top-secondary',
            'title' => ucwords(WP_ENV),
            'href' => '#',
            'meta' => [
                'class' => 'environment-'.sanitize_html_class(strtolower(WP_ENV)),
            ],
        ]);

        foreach ($this->stages as $stage => $url) {
            if ($stage === WP_ENV) {
                continue;
            }

            if ($subdomain_multisite) {
                $url = $this->multisite_url($url);
            }

            $url = apply_filters('bedrock/stage_switcher_url', rtrim($url, '/').$request_uri, $url, $stage);

            $admin_bar->add_menu([
                'id' => "stage_$stage",
                'parent' => 'environment',
                'title' => ucwords($stage),
                'href' => $url,
                'meta' => [
                    'class' => 'environment-'.sanitize_html_class(strtolower($stage)),
                ],
            ]);
        }
    }

    public function admin_css(): void
    { ?>
    <style>
      #wpadminbar #wp-admin-bar-environment > .ab-item:before {
        content: "\f177";
        top: 2px;
      }

      <?php
        $environment_colors = apply_filters('bedrock/stage_switcher_colors', self::default_environment_colors());
        if (! empty($environment_colors) && ! empty($this->stages)) {
            // Style the current environment (parent menu item)
            if (defined('WP_ENV') && ! empty($environment_colors[WP_ENV])) { ?>
          #wpadminbar #wp-admin-bar-environment {
            background-color: <?= esc_attr($environment_colors[WP_ENV]); ?>;
          }
      <?php
            }

            // Style other environments (child menu items)
            foreach ($this->stages as $stage => $url) {
                if (empty($environment_colors[$stage])) {
                    continue;
                } ?>
          #wpadminbar #wp-admin-bar-stage_<?= sanitize_html_class(strtolower($stage)); ?> {
            background-color: <?= esc_attr($environment_colors[$stage]); ?>;
          }
      <?php
            }
        } ?>
    </style>
    <?php
    }

    private function multisite_url(string $url): string
    {
        // Normalize URL to ensure it can be successfully parsed
        $url = esc_url($url);

        $current_host = wp_parse_url(get_home_url(get_current_blog_id()), PHP_URL_HOST);
        $current_stage_host_suffix = wp_parse_url($this->stages[WP_ENV], PHP_URL_HOST);
        $target_stage_host_suffix = wp_parse_url($url, PHP_URL_HOST);

        if (! is_string($current_host) || ! is_string($current_stage_host_suffix) || ! is_string($target_stage_host_suffix)) {
            return $url;
        }

        // Using preg_replace to anchor to the end of the host string
        $target_host = preg_replace('/'.preg_quote($current_stage_host_suffix).'$/', $target_stage_host_suffix, $current_host);
        if (! is_string($target_host)) {
            return $url;
        }

        // Use the stage URL as the base for replacement to keep scheme/port
        return str_replace($target_stage_host_suffix, $target_host, $url);
    }

    private function relative_uri(string $request_uri): string
    {
        $request_path = wp_parse_url($request_uri, PHP_URL_PATH);
        $query = wp_parse_url($request_uri, PHP_URL_QUERY);

        if (! is_string($request_path)) {
            return $request_uri;
        }

        // Try runtime site path first, fall back to ENVIRONMENTS config path
        $site_path = rtrim((string) wp_parse_url(site_url('/'), PHP_URL_PATH), '/');
        $env_path = rtrim((string) wp_parse_url($this->stages[WP_ENV] ?? '', PHP_URL_PATH), '/');

        $relative_path = null;

        if ($site_path !== '' && ($request_path === $site_path || str_starts_with($request_path, $site_path.'/'))) {
            $relative_path = substr($request_path, strlen($site_path));
        } elseif ($env_path !== '' && ($request_path === $env_path || str_starts_with($request_path, $env_path.'/'))) {
            $relative_path = substr($request_path, strlen($env_path));
        }

        if ($relative_path === null) {
            return $request_uri;
        }

        $relative_path = '/'.ltrim($relative_path, '/');

        return $relative_path.(is_string($query) && $query !== '' ? "?{$query}" : '');
    }

    private static function default_environment_colors(): array
    {
        return [
            'development' => 'firebrick',
            'staging' => 'chocolate',
            'production' => 'transparent',
        ];
    }
}

new StageSwitcher;
