<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

class Admin {

    static $page;
    static $nonce_action;

    static function init() {
        self::$page = '?page=' . Plugin::PREFIX;
        self::$nonce_action = Plugin::PREFIX . '-action';

        $version = get_option(Plugin::PREFIX . '_version');
        if (Plugin::VERSION !== $version) {
            include_once __DIR__ . '/includes/activate.php';
            update_option(Plugin::PREFIX . '_version', Plugin::VERSION, false);
        }

        // @phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not relevant here
        if (isset($_GET['page']) && $_GET['page'] === Plugin::PREFIX) {
            add_action('admin_enqueue_scripts', function ($hook) {
                wp_enqueue_style(Plugin::PREFIX, plugin_dir_url(__FILE__) . '/assets/admin.css', [], Plugin::VERSION);
            });
        }

        add_action('admin_menu', function () {

            add_options_page(
                    'MCP Servers', 'MCP Servers', 'administrator', Plugin::PREFIX,
                    function () {
                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                        $subpage = sanitize_key($_GET['subpage'] ?? '');
                        switch ($subpage) {
                            case 'settings':
                                include __DIR__ . '/settings.php';
                                break;
                            case 'servers-list':
                                include __DIR__ . '/servers/list.php';
                                break;
                            case 'servers-edit':
                                include __DIR__ . '/servers/edit.php';
                                break;
                            case 'logs':
                                include __DIR__ . '/logs.php';
                                break;
                            default:
                                include __DIR__ . '/index.php';
                        }
                    }
            );
        });
    }
}

Admin::init();

