<?php

defined('ABSPATH') || exit;

$version = get_option('satollo_mcp_version');
if (SATOLLO_MCP_VERSION !== $version) {
    include_once __DIR__ . '/includes/activate.php';
    update_option('satollo_mcp_version', SATOLLO_MCP_VERSION, false);
}

if (isset($_GET['page']) && $_GET['page'] === 'satollo-mcp') {
    add_action('admin_enqueue_scripts', function ($hook) {
        wp_enqueue_style('satollo-mcp', plugin_dir_url(__FILE__) . '/assets/admin.css', [], SATOLLO_MCP_VERSION);
    });
}

add_action('admin_menu', function () {

    add_options_page(
            'MCP', 'MCP', 'administrator', 'satollo-mcp',
            function () {
                $subpage = $_GET['subpage'] ?? '';
                switch ($subpage) {
                    case 'settings':
                        include __DIR__ . '/settings.php';
                        break;
                    case 'list':
                        include __DIR__ . '/list.php';
                        break;
                    case 'list-edit':
                        include __DIR__ . '/edit.php';
                        break;
                    default:
                        include __DIR__ . '/index.php';
                }
            }
    );
});

