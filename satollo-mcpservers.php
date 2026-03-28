<?php

namespace Satollo\McpServers;

/*
  Plugin Name: Satollo MCPServers
  Plugin URI: https://www.satollo.net/plugins/mcpservers
  Description: Create MCP servers using the WP abilties
  Version: 0.0.6
  Requires PHP: 8.1
  Requires at least: 6.9
  Author: Stefano Lissa
  Author URI: https://www.satollo.net
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
  Update URI: satollo-mcpservers
 */

defined('ABSPATH') || exit;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

class Plugin {

    const VERSION = '0.0.6';
    const SLUG = 'satollo-mcpservers';
    const PREFIX = 'mcpservers';

    static $settings = null;

    static function init() {
        add_action('init', function () {
            require_once __DIR__ . '/vendor/autoload_packages.php';
            \WP\MCP\Core\McpAdapter::instance();
        });
    }

    static function get_settings() {
        if (!self::$settings) {
            self::$settings = (object) get_option(self::PREFIX . '_settings', []);
        }
        return self::$settings;
    }

    static function get_option($name) {
        return get_option(self::PREFIX . '_' . $name);
    }

    static function update_option($name, $value, $autoload = false) {
        update_option(self::PREFIX . '_' . $name, $value, $autoload);
    }

    static function log($text) {
        if (WP_DEBUG) {
            //error_log('MCPServers > ' . $text);
        }
    }
}

Plugin::init();

add_action('mcp_adapter_init', function ($adapter) {
    global $wpdb;

    require_once __DIR__ . '/includes/observer.php';

    // All available abilities
    $abilities = wp_get_abilities();

    // Load all defined MCP servers
    $servers = $wpdb->get_results("select * from {$wpdb->prefix}mcpservers_servers");
    $settings = Plugin::get_settings();

    foreach ($servers as $server) {

        $categories = wp_parse_list($server->categories ?? []);
        if (empty($categories)) {
            Plugin::log('No categories for the server ' . $server->id);
            continue;
        }

        // List of the ability names, since the MCP adaper want only the names, not the ability objects (?)
        $ability_names = [];
        foreach ($abilities as $ability) {
            /** @var WP_Ability $ability */
            if (in_array($ability->get_category(), $categories)) {
                $ability_names[] = $ability->get_name();
            }
        }

        $route = $server->route ?: 'mcp-' . $server->id;
        $namespace = $server->namespace ?: 'mcpservers';

        /** @var WP\MCP\Core\McpAdapter $adapter */
        $r = $adapter->create_server(
                'mcp' . $server->id, // Unique server identifier
                $namespace, // REST API namespace
                $route, // REST API route
                $server->name, // Server name
                $server->description, // Server description
                'v1.0.0', // Server version
                [// Transport methods
                    \WP\MCP\Transport\HttpTransport::class, // Recommended: MCP 2025-06-18 compliant
                ],
                \WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class, // Error handler
                ($settings->logging ?? false) ? McpObservabilityHandler::class : \WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
                //WP_DEBUG ? \WP\MCP\Infrastructure\Observability\ErrorLogMcpObservabilityHandler::class : \WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class, // Observability handler
                $ability_names, // Abilities to expose as tools
                [], // Resources (optional)
                [], // Prompts (optional)
        );

        if (is_wp_error($r)) {
            /** @var WP_Error $r */
            Plugin::log($r->get_error_message());
        } else {
            /** @var \WP\MCP\Core\McpAdapter $r */
            Plugin::log('Server created: ' . $server->id);
        }
    }
});

if (is_admin()) {
    require_once __DIR__ . '/admin/admin.php';
}

if (is_admin() || defined('DOING_CRON') && DOING_CRON) {
    if (file_exists(__DIR__ . '/includes/repo.php')) {
        require_once __DIR__ . '/includes/repo.php';
    }
}

// Daily cleanup process
if (defined('DOING_CRON') && DOING_CRON) {
    add_action('mcpservers_clean_logs', function () {
        global $wpdb;
        $settings = Plugin::get_settings();
        $days = (int) ($settings->log_days ?? 30);
        $days = max($days, 1);
        $wpdb->query($wpdb->prepare("delete from `{$wpdb->prefix}mcpservers_logs` where created < date_sub(now(), interval %d day)", $days));
    });
}
