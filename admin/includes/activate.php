<?php

defined('ABSPATH') || exit;

global $wpdb, $charset_collate;

if (WP_DEBUG) {
    error_log('Satollo MCP > Activating');
}

require_once ABSPATH . 'wp-admin/includes/upgrade.php'; // Isn't there a constant for the admin inclusion path?

$sql = "CREATE TABLE `" . $wpdb->prefix . "satollo_mcp_servers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `status` int NOT NULL DEFAULT 0,
            `name` varchar(200) NOT NULL DEFAULT '',
            `description` varchar(500) NOT NULL DEFAULT '',
            `categories` varchar(1000) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
            ) $charset_collate;";

dbDelta($sql);

if ($wpdb->last_error) {
    error_log('Satollo MCP > ' . $wpdb->last_error);
}

// Cleanup process
//if (!wp_next_scheduled('satollo_mcp_clean_logs') && (!defined('WP_INSTALLING') || !WP_INSTALLING)) {
//    wp_schedule_event(time() + 30, 'daily', 'satollo_mcp_clean_logs');
//}
