<?php
// If uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit ();
}

// Remove capabilities
if (function_exists('wprss_remove_caps')) {
    wprss_remove_caps();
}
