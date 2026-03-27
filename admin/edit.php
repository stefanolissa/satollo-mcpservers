<?php
defined('ABSPATH') || exit;

/** @var wpdb $wpdb */
global $wpdb;

$server_id = (int) $_GET['id'] ?? 0;
$server = $wpdb->get_row($wpdb->prepare("select * from {$wpdb->prefix}satollo_mcp_servers where id=%d limit 1", $server_id), ARRAY_A);
if (!$server) {
    die('Invalid ID');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_admin_referer('satollo-mcp-action');

    if (isset($_POST['save'])) {
        $data = wp_unslash($_POST['data'] ?? []);

        $row['name'] = wp_strip_all_tags($data['name']) ?: 'Server';
        $row['description'] = wp_kses_post($data['description']);
        $row['categories'] = implode(',', $data['categories'] ?? []);
        $row['route'] = sanitize_key($data['route'] ?? '');
        $row['namespace'] = sanitize_key($data['namespace'] ?? '');

        $wpdb->update($wpdb->prefix . 'satollo_mcp_servers', $row, ['id' => $server['id']]);
        if (WP_DEBUG && $wpdb->last_error) {
            die(esc_html($wpdb->last_error));
        }
    }
} else {
    $data = $server;
    $data['categories'] = wp_parse_list($server['categories'] ?? []);
}

$categories = wp_get_ability_categories();
?>
<?php include __DIR__ . '/menu.php'; ?>
<div class="wrap">
    <div class="satollo-notice satollo-notice-warning">
        Warning: abilities are provided by third parties and they are responsible for permission check.
    </div>

    <form method="post">
        <?php wp_nonce_field('satollo-mcp-action'); ?>
        <table class="form-table">

            <tbody>

                <tr>
                    <th>
                        Name
                    </th>
                    <td>
                        <input type="text" name="data[name]" size="40" value="<?php echo esc_attr($data['name'] ?? ''); ?>" placeholder="">
                        <p class="description"></p>
                    </td>

                </tr>
                <tr>
                    <th>
                        Description
                    </th>
                    <td>
                        <textarea name="data[description]" cols="40" placeholder=""><?php echo esc_html($data['description']); ?></textarea>
                        <p class="description">
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Expose those abilitiy categories</h3>

        <?php foreach ($categories as $category) { ?>
            <label>
                <input type="checkbox" name="data[categories][]" value="<?php echo esc_attr($category->get_slug()) ?>" <?php echo in_array($category->get_slug(), $data['categories']) ? 'checked' : ''; ?>>
                <?php echo esc_html($category->get_label()) ?>
                <br>
                <small><?php echo esc_html($category->get_description()) ?></small>
            </label>
            <br>
        <?php } ?>

        <p><button name="save" class="button button-primary">Save</button></p>
    </form>
</div>