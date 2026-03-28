<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

$post = wp_unslash($_POST);

if (isset($post['save'])) {
    check_admin_referer('mcpservers-action');
    $data = $post['data'] ?? [];
    // No lazy loading???
    update_option('mcpservers_settings', $data);
}

$data = get_option('mcpservers_settings', []);
?>

<?php include __DIR__ . '/menu.php'; ?>
<div class="wrap">

    <form method="post">
        <?php wp_nonce_field('mcpservers-action'); ?>

        <table class="form-table" role="presentation">
            <tr>
                <th>
                    <?php esc_html_e('Logging', 'satollo-mcpservers'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" value="1" name="data[logging]" <?php echo isset($data['logging']) ? 'checked' : ''; ?>> Enable
                    </label>
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e('Debug', 'satollo-mcpservers'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" value="1" name="data[debug]" <?php echo isset($data['debug']) ? 'checked' : ''; ?>> Enable
                    </label>
                    <p class="description"><?php esc_html_e('Sends MCP server events to error_log', 'satollo-mcpservers'); ?></p>
                </td>
            </tr>
        </table>

        <p><button name="save" class="button button-primary"><?php esc_html_e('Save', 'satollo-mcpservers'); ?></button></p>

    </form>

    <h3>Debug</h3>
    <pre><?php echo esc_html(print_r($data, true)); ?></pre>

</div>