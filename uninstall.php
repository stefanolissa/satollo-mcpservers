<?php
defined('WP_UNINSTALL_PLUGIN') || exit;

delete_option('satollo_mcp_settings');
delete_option('satollo_mcp_version');
wp_unschedule_hook('satollo_mcp_clean_logs');

global $wpdb;
//$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}satollo_mcp_servers");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}satollo_mcp_logs");
