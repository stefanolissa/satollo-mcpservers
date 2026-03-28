<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

/** @var \wpdb $wpdb */
global $wpdb;

$request = wp_unslash($_REQUEST);
$action = $request['action'] ?? '';

if ($action) {
    check_admin_referer(Admin::$nonce_action);

    if ($action === 'new') {
        $wpdb->insert($wpdb->prefix . 'mcpservers_servers', ['name' => __('New MCP Servers', 'satollo-mcpservers')]);
        echo '<script>location.href="?page=mcpservers&subpage=servers-edit&id=' . ((int) $wpdb->insert_id) . '";</script>';
        return;
    }

    if ($action === 'trash') {
        $wpdb->update($wpdb->prefix . 'mcpservers_servers', ['deleted' => 1], ['id' => $request['id']]);
        echo '<script>location.href="?page=mcpservers&subpage=servers-list";</script>';
        return;
    }

    if ($action === 'restore') {
        $wpdb->update($wpdb->prefix . 'mcpservers_servers', ['deleted' => 0], ['id' => $request['id']]);
        echo '<script>location.href="?page=mcpservers&subpage=servers-list";</script>';
        return;
    }

    if ($action === 'delete') {
        $wpdb->delete($wpdb->prefix . 'mcpservers_servers', ['id' => $request['id']]);
        echo '<script>location.href="?page=mcpservers&subpage=servers-list";</script>';
        return;
    }
}

class Servers_List_Table extends \WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => 'server',
            'plural' => 'servers',
            'ajax' => false,
        ]);
    }

    public function get_columns() {
        $columns = [
            //'cb' => '<input type="checkbox">',
            'name' => __('Name', 'satollo-mcpservers'),
            'description' => __('Description', 'satollo-mcpservers'),
            'url' => 'URL',
            'categories' => __('Description', 'satollo-mcpservers'),
            'actions' => __('Actions', 'satollo-mcpservers'),
        ];
        return $columns;
    }

    public function prepare_items() {
        global $wpdb;

        $items = $wpdb->get_results("select * from {$wpdb->prefix}mcpservers_servers");

        $columns = $this->get_columns();
        $hidden = []; // You can specify columns to hide here.
        $sortable = []; // You can specify sortable columns here.
        $this->_column_headers = [$columns, $hidden, $sortable];

        // This is where you would implement pagination logic.
        $per_page = 20; // Number of items to display per page.
        $current_page = $this->get_pagenum();
        $total_items = count($items);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $this->items = array_slice($items, (($current_page - 1) * $per_page), $per_page);
    }

//    public function column_cb($item) {
//        return '<input type="checkbox" name="data[abilities][]" value="' . esc_attr($item->get_name()) . '"'
//                . (in_array($item->get_name(), $this->enabled_abilities) ? 'checked' : '')
//                . '>';
//    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'name':
                return '<a href="?page=mcpservers&subpage=servers-edit&id=' . ((int) $item->id) . '">' . esc_html($item->name) . '</a>';
            case 'description':
                return esc_html($item->description);
            case 'url':
                return esc_html(get_rest_url(null, '/mcpservers/mcp-' . $item->id));
            case 'categories':
                return esc_html($item->categories);
            case 'actions':
                if ($item->deleted ?? false) {
                    return '<a href="' . esc_attr(wp_nonce_url(Admin::$page . '&subpage=servers-list&action=delete&id=' . ((int) $item->id), Admin::$nonce_action)) . '">' . esc_html__('Delete', 'satollo-mcpservers') . '</a>'
                            . ' <a href="' . esc_attr(wp_nonce_url(Admin::$page . '&subpage=servers-list&action=restore&id=' . ((int) $item->id), Admin::$nonce_action)) . '">' . esc_html__('Restore', 'satollo-mcpservers') . '</a>';
                } else {
                    return '<a href="' . esc_attr(wp_nonce_url(Admin::$page . '&subpage=servers-list&&action=trash&id=' . ((int) $item->id), Admin::$nonce_action)) . '">' . esc_html__('Trash', 'satollo-mcpservers') . '</a>';
                }
            default:
                return '?';
        }
    }
}

$table = new Servers_List_Table();
$table->prepare_items();
?>

<?php include __DIR__ . '/../menu.php'; ?>
<div class="wrap">
    <div class="satollo-notice satollo-notice-warning">
        Warning: abilities are provided by third parties and they are responsible for permission check.
    </div>
    <form method="post">
<?php wp_nonce_field(Admin::$nonce_action); ?>
        <input type="hidden" name="action" value="new">
        <p><button class="button button-primary"><?php esc_html_e('Add new', 'satollo-mcpservers') ?></button></p>
    </form>

    <form method="post">
<?php $table->display(); ?>
    </form>

    <div class="satollo-notice satollo-notice-warning">
        If you open the server URL you should get a "Sorry, you are not allowed to do that". That's correct, the server is running.
    </div>

</div>