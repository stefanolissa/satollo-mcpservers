<?php
namespace Satollo\McpServers;

defined('ABSPATH') || exit;
?>

<div id="satollo-menu">
    <div id="satollo-menu-title">MCP</div>
    <div id="satollo-menu-nav">
        <ul>
            <li><a href="?page=mcpservers&subpage=index"><?php esc_html_e('Home', 'satollo-mcpservers') ?></a></li>
            <li><a href="?page=mcpservers&subpage=servers-list"><?php esc_html_e('Servers', 'satollo-mcpservers') ?></a></li>
            <li><a href="?page=mcpservers&subpage=settings"><?php esc_html_e('Settings', 'satollo-mcpservers') ?></a></li>
            <li><a href="?page=mcpservers&subpage=logs"><?php esc_html_e('Logs', 'satollo-mcpservers') ?></a></li>
            <li><a href="https://www.satollo.net/plugins/mcpservers" target="_blank"><?php esc_html_e('Help', 'satollo-mcpservers') ?></a></li>
        </ul>
    </div>

    <div></div>
</div>
<script>
    jQuery(function () {
        jQuery('#satollo-menu-nav a').each(function () {
            if (location.href.indexOf(this.href) >= 0) {
                jQuery(this).addClass('satollo-active');
            }
        });
    });
</script>
