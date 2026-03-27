<?php
defined('ABSPATH') || exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('satollo-mcp-save'); // by the wp list table...
    $data = wp_unslash($_POST['data'] ?? []);
    update_option('satollo_mcp_settings', $data);
}

$data = get_option('satollo_mcp_settings', []);
?>

<?php include __DIR__ . '/menu.php'; ?>
<div class="wrap">

    <form method="post">
        <?php wp_nonce_field('satollo-mcp-save'); ?>
        <p>
            No global settings yet!
        </p>

        <table class="form-table" role="presentation">
            <tr>
                <th>
                    <?php esc_html_e('Logging', 'satollo-mcp'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" value="1" name="data[logging]" <?= isset($data['logging']) ? 'checked' : ''; ?>> Enable
                    </label>
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e('Debug', 'satollo-mcp'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" value="1" name="data[debug]" <?= isset($data['debug']) ? 'checked' : ''; ?>> Enable
                    </label>
                    <p class="description">Sends MCP server events to error_log.</p>
                </td>
            </tr>
        </table>

        <p><button name="save" class="button button-primary">Save</button></p>

    </form>

    <h3>Debug</h3>
    <pre><?php echo esc_html(print_r($data, true)); ?></pre>

</div>