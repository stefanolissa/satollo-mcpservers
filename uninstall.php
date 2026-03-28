<?php
namespace Satollo\McpServers;

defined('WP_UNINSTALL_PLUGIN') || exit;

global $wpdb;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange

delete_option('mcpservers_settings');
delete_option('mcpservers_version');
wp_unschedule_hook('mcpservers_clean_logs');

//$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mcpservers_servers");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mcpservers_logs");
