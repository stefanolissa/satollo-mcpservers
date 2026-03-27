<?php

/*
  Plugin Name: Satollo MCP Servers
  Plugin URI: https://www.satollo.net/plugins/mcp
  Description: MCP servers using the WP abilties
  Version: 0.0.5
  Requires PHP: 8.1
  Requires at least: 6.9
  Author: Stefano Lissa
  Author URI: https://www.satollo.net
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
  Update URI: satollo-mcp
 */

defined('ABSPATH') || exit;

define('SATOLLO_MCP_VERSION', '0.0.5');

add_action('init', function () {
    require_once __DIR__ . '/vendor/autoload_packages.php';
    WP\MCP\Core\McpAdapter::instance();
});

add_action('mcp_adapter_init', function ($adapter) {
    global $wpdb;

    require_once __DIR__ . '/includes/observer.php';

    // All available abilities
    $abilities = wp_get_abilities();

    // Load all defined MCP servers
    $servers = $wpdb->get_results("select * from {$wpdb->prefix}satollo_mcp_servers");
    $settings = get_option('satollo_mcp_settings', []);

    foreach ($servers as $server) {

        $categories = wp_parse_list($server->categories ?? []);
        if (empty($categories)) {
            if (WP_DEBUG) {
                error_log('No categories for the server ' . $server->id);
            }
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
        $namespace = $server->namespace ?: 'mcps';

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
                ($settings['logging'] ?? false) ? SatolloMcpObservabilityHandler::class : \WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
                //WP_DEBUG ? \WP\MCP\Infrastructure\Observability\ErrorLogMcpObservabilityHandler::class : \WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class, // Observability handler
                $ability_names, // Abilities to expose as tools
                [], // Resources (optional)
                [], // Prompts (optional)
        );

        if (WP_DEBUG) {
            if (is_wp_error($r)) {
                /** @var WP_Error $r */
                error_log($r->get_error_message());
            } else {
                /** @var \WP\MCP\Core\McpAdapter $r */
                error_log('Server created: ' . $server->id);
            }
        }
    }
});

if (is_admin()) {
    require_once __DIR__ . '/admin/admin.php';
}

if (is_admin() || defined('DOING_CRON') && DOING_CRON) {
    require_once __DIR__ . '/includes/repo.php';
}

// Daily cleanup process
add_action('satollo_mcp_clean_logs', 'satollo_mcp_clean_logs');

function satollo_mcp_clean_logs() {
    global $wpdb;
    $settings = get_option('satollo-mcp');
    $days = (int) ($settings['log_days'] ?? 30);
    $days = max($days, 1);
    $wpdb->query($wpdb->prepare("delete from `{$wpdb->prefix}satollo_mcp_logs` where created < date_sub(now(), interval %d day)", $days));
}