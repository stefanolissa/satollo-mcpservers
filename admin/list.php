<?php
defined('ABSPATH') || exit;

/** @var wpdb $wpdb */
global $wpdb;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('satollo-mcp-action');

    if (isset($_POST['add'])) {
        $wpdb->insert($wpdb->prefix . 'satollo_mcp_servers', ['name' => 'New assistant']);
        echo '<script>location.href="?page=satollo-mcp&subpage=list-edit&id=' . ((int) $wpdb->insert_id) . '";</script>';
        return;
    }
}

class Servers_List_Table extends WP_List_Table {

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
            'name' => 'Name',
            'description' => 'Description',
            'uri' => 'URI',
            'categories' => 'Categories',
        ];
        return $columns;
    }

    public function prepare_items() {
        global $wpdb;

        $items = $wpdb->get_results("select * from {$wpdb->prefix}satollo_mcp_servers");

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

    public function column_cb($item) {
        return '<input type="checkbox" name="data[abilities][]" value="' . esc_attr($item->get_name()) . '"'
                . (in_array($item->get_name(), $this->enabled_abilities) ? 'checked' : '')
                . '>';
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'name':
                return '<a href="?page=satollo-mcp&subpage=list-edit&id=' . $item->id . '">' . esc_html($item->name) . '</a>';
            case 'description':
                return esc_html($item->description);
            case 'uri':
                return get_rest_url(null, '/mcps/mcp-' . $item->id);
            case 'categories':
                return esc_html($item->categories);
            default:
                return '?';
        }
    }
}

$table = new Servers_List_Table();
$table->prepare_items();
?>

<?php include __DIR__ . '/menu.php'; ?>
<div class="wrap">
    <div class="satollo-notice satollo-notice-warning">
        Warning: abilities are provided by third parties and they are responsible for permission check.
    </div>
    <form method="post">
        <?php wp_nonce_field('satollo-mcp-action'); ?>
        <p><button name="add" class="button button-primary">Add new</button></p>
    </form>

    <form method="post">
        <?php $table->display(); ?>
    </form>

    <div class="satollo-notice satollo-notice-warning">
        If you open the server URL you should get a "Sorry, you are not allowed to do that". That's correct, the server is running.
    </div>


</div>