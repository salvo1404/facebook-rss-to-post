<?php
/*
Plugin Name:       Facebook RSS to Post
Plugin URI:        https://github.com/salvo1404/facebook-rss-to-post
Description:       This plugin allows you to import RSS and Atom feeds from your favourite Facebook page and transform items to Wordpress's posts.
Version:           1.0.0
Author:            Salvatore Balzano
Author URI:        https://github.com/salvo1404
License:           GPLv2 or later
Domain Path:       /lang
*/

/**
 * Define constants used by the plugin.
 */

if (!defined('FB_RSS_PATH')) {
    define('FB_RSS_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('FB_RSS_URL')) {
    define('FB_RSS_URL', trailingslashit(plugin_dir_url(__FILE__)));
}

if (!defined('FB_RSS_BASENAME')) {
    define('FB_RSS_BASENAME', plugin_basename(__FILE__));
}

// Set the constant path to the plugin's includes directory.
if (!defined('FB_RSS_INC')) {
    define('FB_RSS_INC', FB_RSS_PATH . trailingslashit('includes'), true);
}

if (!defined('FB_RSS_VERSION')) {
    define('FB_RSS_VERSION', '1.0.0');
}

// Set the constant path to the plugin's log file.
if (!defined('FB_RSS_LOG_FILE')) {
    define('FB_RSS_LOG_FILE', FB_RSS_PATH . 'log', true);
}
if (!defined('FB_RSS_LOG_FILE_EXT')) {
    define('FB_RSS_LOG_FILE_EXT', '.txt', true);
}

// the main loader class
include_once(FB_RSS_INC . '/admin/class-fb-rss-to-post.php');
include_once(FB_RSS_INC . '/admin/class-fb-rss-to-post-listener.php');
include_once(FB_RSS_INC . '/admin/class-fb-rss-to-post-engine-old.php');
include_once(FB_RSS_INC . '/admin/class-fb-rss-to-post-engine.php');

$fb_rss_to_post = new FbRssToPost();
