<?php
defined('ABSPATH') || exit;

?>

<div id="satollo-menu">
    <div id="satollo-menu-title">MCP</div>
    <div id="satollo-menu-nav">
        <ul>
            <li><a href="?page=satollo-mcp&subpage=index">Home</a></li>
            <li><a href="?page=satollo-mcp&subpage=list">Servers</a></li>
            <li><a href="?page=satollo-mcp&subpage=settings">Settings</a></li>
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
