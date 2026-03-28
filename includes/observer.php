<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

class McpObservabilityHandler implements \WP\MCP\Infrastructure\Observability\Contracts\McpObservabilityHandlerInterface {

    /**
     * Emit a countable event for tracking with optional timing data.
     *
     * This method does nothing and is used when no observability tracking is desired.
     *
     * @param string     $event The event name to record.
     * @param array      $tags Optional tags to attach to the event.
     * @param float|null $duration_ms Optional duration in milliseconds for timing measurements.
     * @global wpdb $wpdb
     * @return void
     */
    public function record_event(string $event, array $tags = array(), ?float $duration_ms = null): void {
        global $wpdb;

        static $error_log_handler = null;

        $settings = Plugin::get_settings();

        if ($event === 'mcp.request') {
            $session_id = $tags['session_id'] ?? '';
            if (!$session_id) {
                $session_id = $tags['new_session_id'] ?? '';
            }
            $wpdb->insert($wpdb->prefix . 'mcpservers_logs',
                    ['event' => $event, 'server_id' => $tags['server_id'] ?? '',
                        'method' => $tags['method'] ?? '',
                        'session_id' => $session_id,
                        'client_name' => $tags['params']['client_name'] ?? '',
            ]);
            if ($wpdb->last_error) {
                Plugin::log($wpdb->last_error);
            }
        }

        if ($settings->debug ?? false) {
            if (!$error_log_handler) {
                $error_log_handler = new \WP\MCP\Infrastructure\Observability\ErrorLogMcpObservabilityHandler();
            }
            $error_log_handler->record_event($event, $tags, $duration_ms);
        }
    }
}
