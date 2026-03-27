<?php
defined('ABSPATH') || exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('bulk-abilities'); // by the wp list table...
    $settings = wp_unslash($_POST['data']);
    update_option('satollo_mcp_settings', $settings ?? []);
}

$settings = get_option('satollo_mcp_settings', []);
?>

<?php include __DIR__ . '/menu.php'; ?>
<div class="wrap">

    <form method="post">

        <p>
            No global settings yet!
        </p>

        <p><button name="save" class="button button-primary">Save</button></p>

    </form>

</div>