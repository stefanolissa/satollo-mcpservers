<?php

namespace Satollo\McpServers;

defined('ABSPATH') || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- We are inside a function here
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

/** @var wpdb $wpdb */
global $wpdb;

$get = wp_unslash($_GET);

$server = $wpdb->get_row($wpdb->prepare("select * from {$wpdb->prefix}mcpservers_servers where id=%d limit 1", $get['id']), ARRAY_A);
if (!$server) {
    die('Invalid ID');
}

$post = wp_unslash($_POST);

$categories = wp_get_ability_categories();
$abilities = wp_get_abilities();

if (isset($post['save'])) {
    check_admin_referer(Admin::$nonce_action);

    $data = $post['data'] ?? [];

    $row['name'] = wp_strip_all_tags($data['name']) ?: 'Server';
    $row['description'] = wp_kses_post($data['description']);
    $data['categories'] ??= [];
    $row['categories'] = implode(',', $data['categories']);
    $data['abilities'] ??= [];

    // Keep only the abilities of partial categories
    $row['abilities'] = [];
    foreach ($data['abilities'] as $ability_name) {
        $ability = wp_get_ability($ability_name);
        if ($ability && in_array($ability->get_category() . '*', $data['categories'])) {
            $row['abilities'][] = $ability_name;
        }
    }
    $data['abilities'] = $row['abilities'];

    $row['abilities'] = implode(',', $row['abilities']);
    $row['route'] = sanitize_key($data['route'] ?? '');
    $row['namespace'] = sanitize_key($data['namespace'] ?? '');

    $wpdb->update($wpdb->prefix . 'mcpservers_servers', $row, ['id' => $server['id']]);
    if (WP_DEBUG && $wpdb->last_error) {
        die(esc_html($wpdb->last_error));
    }
} else {
    $data = $server;
    $data['categories'] = wp_parse_list($server['categories'] ?? []);
    $data['abilities'] = wp_parse_list($server['abilities'] ?? []);
}

?>
<?php include __DIR__ . '/../menu.php'; ?>
<div class="wrap">
    <div class="satollo-notice satollo-notice-warning">
        <?php esc_html_e('Warning: abilities are provided by third parties and they are responsible for permission check.', 'satollo-mcpservers'); ?>
    </div>

    <div class="satollo-notice satollo-notice-warning">
        If no tools are shown when you connected to this MCP Server, please enable only the category "Site" and check again. If
        the tools appears, it means one or more abilities have problems in the schema declaration. Contact the author.
    </div>

    <form method="post">
        <?php wp_nonce_field(Admin::$nonce_action); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <?php esc_html_e('Name', 'satollo-mcpservers'); ?>
                    </th>
                    <td>
                        <input type="text" name="data[name]" size="40" value="<?php echo esc_attr($data['name'] ?? ''); ?>" placeholder="">
                        <p class="description"></p>
                    </td>

                </tr>
                <tr>
                    <th>
                        <?php esc_html_e('Description', 'satollo-mcpservers'); ?>
                    </th>
                    <td>
                        <textarea name="data[description]" cols="40" placeholder=""><?php echo esc_html($data['description']); ?></textarea>
                        <p class="description">
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3><?php esc_html_e('Abilities to expose', 'satollo-mcpservers'); ?></h3>

        <table class="form-table">
            <tbody>
                <?php foreach ($categories as $category) { ?>
                    <?php $slug = $category->get_slug(); ?>
                    <tr>
                        <th>
                            <?php echo esc_html($category->get_label()) ?>
                        </th>
                        <td><!--
                            <label>
                                <input type="checkbox" name="data[categories][]" value="<?php echo esc_attr($category->get_slug()) ?>" <?php echo in_array($category->get_slug(), $data['categories']) ? 'checked' : ''; ?>>
                            <?php echo esc_html($category->get_label()) ?>
                                <small><?php echo esc_html($category->get_description()) ?></small>
                            </label>
                            <br>-->
                            <select name="data[categories][]">
                                <option value="" <?php echo $log_days == 15 ? 'selected' : ''; ?>>Not exposed</option>
                                <option value="<?php echo esc_attr($slug) ?>" <?php echo in_array($slug, $data['categories']) ? 'selected' : ''; ?>>All abilities exposed</option>
                                <option value="<?php echo esc_attr($slug) ?>*" <?php echo in_array($slug . '*', $data['categories']) ? 'selected' : ''; ?>>Specific abilities exposed</option>
                            </select>
                            <br>
                            <?php foreach ($abilities as $ability) { ?>
                                <?php if ($category->get_slug() !== $ability->get_category()) continue; ?>
                                <label>
                                    <input type="checkbox" name="data[abilities][]" value="<?php echo esc_attr($ability->get_name()) ?>" <?php echo in_array($ability->get_name(), $data['abilities']) ? 'checked' : ''; ?>>
                                    <?php echo esc_html($ability->get_label()) ?>

                                    - <small><?php echo esc_html($ability->get_description()) ?></small>
                                </label>

                                <br>
                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>
            </tbody>
        </table>
        <p><button name="save" class="button button-primary"><?php esc_html_e('Save', 'satollo-mcpservers'); ?></button></p>
    </form>
</div>