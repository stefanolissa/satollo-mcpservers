<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

class Activate {

    static function init() {


        global $wpdb, $charset_collate;

//if (WP_DEBUG) {
//    error_log('Satollo MCP > Activating');
//}

        require_once ABSPATH . 'wp-admin/includes/upgrade.php'; // Isn't there a constant for the admin inclusion path?

        $sql = "CREATE TABLE `{$wpdb->prefix}mcpservers_servers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `deleted` int NOT NULL DEFAULT 0,
            `status` int NOT NULL DEFAULT 0,
            `name` varchar(200) NOT NULL DEFAULT '',
            `route` varchar(50) NOT NULL DEFAULT '',
            `namespace` varchar(50) NOT NULL DEFAULT '',
            `description` varchar(500) NOT NULL DEFAULT '',
            `categories` varchar(1000) NOT NULL DEFAULT '',
            `abilities` TEXT NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
            ) $charset_collate;";

        dbDelta($sql);

//if ($wpdb->last_error) {
//    error_log('Satollo MCP > ' . $wpdb->last_error);
//}

        $sql = "CREATE TABLE `{$wpdb->prefix}mcpservers_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `event` varchar(100) NOT NULL DEFAULT '',
            `server_id` varchar(100) NOT NULL DEFAULT '',
            `method` varchar(100) NOT NULL DEFAULT '',
            `client_name` varchar(100) NOT NULL DEFAULT '',
            `session_id` varchar(100) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
            ) $charset_collate;";

        dbDelta($sql);

//if ($wpdb->last_error) {
//    error_log('Satollo MCP > ' . $wpdb->last_error);
//}
// Cleanup process
        if (!wp_next_scheduled('mcpservers_clean_logs') && (!defined('WP_INSTALLING') || !WP_INSTALLING)) {
            wp_schedule_event(time() + 30, 'daily', 'mcpservers_clean_logs');
        }
    }
}

Activate::init();
