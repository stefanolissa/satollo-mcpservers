<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

class Repository {

    const REPO_NAME = 'mcpservers';
    const NAME = 'Satollo MCP Servers';

    static function init() {
        add_filter('update_plugins_' . Plugin::SLUG, function ($update, $plugin_data, $plugin_file, $locales) {
            Plugin::log('update_plugins call');

            $data = Plugin::get_option('update_data');
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ($data && $data->updated < time() - WEEK_IN_SECONDS || isset($_GET['force-check'])) {
                $data = false;
            }

            if (!$data) {
                $response = wp_remote_get('https://www.satollo.net/repo/' . self::REPO_NAME . '/plugin.json');
                $data = json_decode(wp_remote_retrieve_body($response));
                if (is_object($data)) {
                    $data->updated = time();
                    Plugin::update_option('update_data', $data, false);
                }
            }

            if (isset($data->version)) {

                $update = [
                    'version' => $data->version,
                    'slug' => Plugin::SLUG,
                    'url' => 'https://www.satollo.net/plugins/' . self::REPO_NAME,
                    'package' => 'https://www.satollo.net/repo/' . self::REPO_NAME . '/' . self::REPO_NAME . '.zip'
                ];
                return $update;
            } else {
                return false;
            }
        }, 0, 4);

        add_filter('plugins_api', function ($res, $action, $args) {

            if ($action !== 'plugin_information' || $args->slug !== Plugin::SLUG) {
                return $res;
            }

            Plugin::log('plugin_api call');

            $response = wp_remote_get('https://www.satollo.net/repo/' . self::REPO_NAME . '/CHANGELOG.md');
            $changelog = '';
            if (wp_remote_retrieve_response_code($response) == '200') {
                $changelog = wp_remote_retrieve_body($response);
                $changelog = self::render_markdown($changelog);
            }

            $response = \wp_remote_get('https://www.satollo.net/repo/' . self::REPO_NAME . '/README.md');
            $readme = '';
            if (\wp_remote_retrieve_response_code($response) == '200') {
                $readme = wp_remote_retrieve_body($response);
                $readme = self::render_markdown($readme);
            }

            $res = new \stdClass();
            $res->name = self::NAME;
            $res->slug = Plugin::SLUG;
            $res->version = Plugin::VERSION;
            $res->author = '<a href="https://www.satollo.net">Stefano Lissa</a>';
            $res->homepage = 'https://www.satollo.net/plugins/' . self::REPO_NAME;
            $res->download_link = 'https://www.satollo.net/repo/' . self::REPO_NAME . '/' . self::REPO_NAME . '.zip';

            $res->sections = [
                'description' => $readme,
                'changelog' => $changelog
            ];

            $res->banners = [
                'low' => 'https://www.satollo.net/repo/' . self::REPO_NAME . '/banner.png',
                'high' => 'https://www.satollo.net/repo/' . self::REPO_NAME . '/banner.png'
            ];

            $res->icons = [
                '1x' => 'https://www.satollo.net/repo/' . self::REPO_NAME . '/icon.png',
                '2x' => 'https://www.satollo.net/repo/' . self::REPO_NAME . '/icon.png'
            ];

            return $res;
        }, 20, 3);
    }

    static function render_markdown($text) {
        $text = preg_replace('/^### (.*$)/m', '<h4>$1</h4>', $text);
        $text = preg_replace('/^## (.*$)/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^# (.*$)/m', '', $text);
        $text = preg_replace('/^- (.*$)/m', '- $1<br>', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/m', '<strong>$1</strong>', $text);
        $text = preg_replace('/`(.*?)`/m', '<code>$1</code>', $text);
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        $text = wpautop($text, false);
        $text = wp_kses_post($text);
        return $text;
    }
}

Repository::init();
