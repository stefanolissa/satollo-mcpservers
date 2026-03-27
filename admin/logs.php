<?php
defined('ABSPATH') || exit;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- not relevant

global $wpdb;

// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- not necessary
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_admin_referer('satollo-mcp-action');
    if (isset($_POST['clear'])) {
        $wpdb->query("truncate {$wpdb->prefix}satollo_mcp_logs");
    }
}

class Logs_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Ability calls', 'satollo-monitor'),
            'plural' => __('Ability call', 'satollo-monitor'),
            'ajax' => false,
        ]);
    }

    public function get_columns() {
        $columns = [
            'created' => __('Created', 'satollo-mcp'),
            'server_id' => __('Server ID', 'satollo-mcp'),
            'event' => __('Event', 'satollo-mcp'),
            'method' => __('Method', 'satollo-mcp'),
            'client_name' => __('Client', 'satollo-mcp'),
            'session_id' => __('Session', 'satollo-mcp'),
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
        $total_items = (int) $wpdb->get_var("select count(*) from {$wpdb->prefix}satollo_mcp_logs");

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $this->items = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}satollo_mcp_logs order by id desc limit %d offset %d",
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
            case 'data':
                $url = admin_url('admin-ajax.php') . '?action=monitor-ability-data&id=' . rawurlencode($item->id);
                $url = wp_nonce_url($url, 'monitor-ability-data');
                $url .= '&TB_iframe=true'; // Add as last since Thickbox truncate the URL here
                return '<a class="thickbox" href="' . esc_attr($url) . '">Data</a>';
            default:
                return '?';
        }
    }
}

$table = new Logs_List_Table();
$table->prepare_items();
add_thickbox();

$settings = get_option('satollo_mcp_settings', []);
?>
<?php include __DIR__ . '/menu.php'; ?>
<div class="wrap">
    <?php if (!isset($settings['logging'])) { ?>
        <div class="satollo-notice satollo-notice-warning">
            Logging is not active, see the settings page.
        </div>
    <?php } ?>
    
    <form method="post">
        <?php wp_nonce_field('satollo-mcp-action'); ?>
        <button name="clear" class="button button-secondary"><?php esc_html_e('Clear', 'satollo-mcp'); ?></button>
    </form>

    <?php $table->display(); ?>

</div>