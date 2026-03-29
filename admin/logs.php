<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- not relevant
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- We are inside a function here

global $wpdb;

$post = wp_unslash($_POST);

if (isset($post['clear'])) {
    check_admin_referer(Admin::$nonce_action, Admin::$nonce_name);
    $wpdb->query("truncate {$wpdb->prefix}mcpservers_logs");
}

class Logs_List_Table extends \WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Events', 'satollo-mcpservers'),
            'plural' => __('Event', 'satollo-mcpservers'),
            'ajax' => false,
        ]);
    }

    function extra_tablenav($which) {
        if ($which == 'top') {
            // The button will appear next to the 'Apply' bulk action button
            echo '<div class="alignleft actions">';
            echo '<button name="clear" class="button button-secondary">', esc_html_e('Clear', 'satollo-mcpservers'), '</button>';
            echo '</div>';
        }
    }

    public function get_columns() {
        $columns = [
            'created' => __('Created', 'satollo-mcpservers'),
            'server_id' => __('Server ID', 'satollo-mcpservers'),
            'event' => __('Event', 'satollo-mcpservers'),
            'method' => __('Method', 'satollo-mcpservers'),
            'client_name' => __('Client', 'satollo-mcpservers'),
            'session_id' => __('Session', 'satollo-mcpservers'),
        ];
        return $columns;
    }

    public function prepare_items() {
        global $wpdb;

        $columns = $this->get_columns();
        $hidden = [];
        $sortable = ['created'];
        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page = 100;
        $current_page = $this->get_pagenum();
        $total_items = (int) $wpdb->get_var("select count(*) from {$wpdb->prefix}mcpservers_logs");

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $this->items = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}mcpservers_logs order by id desc limit %d offset %d",
                        $per_page, ($current_page - 1) * $per_page));
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'created':
                return esc_html($item->created);
            case 'event':
                return esc_html($item->event);
            case 'server_id':
                return esc_html($item->server_id);
            case 'session_id':
                $color = substr($item->session_id, 1, 5);
                return '<span style="color: #0' . esc_attr($color) . ';">' . esc_html($item->session_id) . '</span>';
            case 'client_name':
                return esc_html($item->client_name);
            case 'method':
                return esc_html($item->method);
            default:
                return '?';
        }
    }
}

$table = new Logs_List_Table();
$table->prepare_items();
add_thickbox();

$settings = Plugin::get_settings();
?>
<?php include __DIR__ . '/menu.php'; ?>
<div class="wrap">
    <?php if (!isset($settings->logging)) { ?>
        <div class="satollo-notice satollo-notice-warning">
            Logging is not active, see the settings page.
        </div>
    <?php } ?>

    <form method="post">
        <?php wp_nonce_field(Admin::$nonce_action, Admin::$nonce_name); ?>

        <?php $table->display(); ?>
    </form>
</div>