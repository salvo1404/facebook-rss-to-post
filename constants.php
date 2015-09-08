<?php

/**
 * Define constants used by the plugin.
 */

define('FB_RSS_PATH', trailingslashit(plugin_dir_path(__FILE__)));

define('FB_RSS_URL', trailingslashit(plugin_dir_url(__FILE__)));

define('FB_RSS_BASENAME', plugin_basename(__FILE__));

// Set the constant path to the plugin's includes directory.
define('FB_RSS_INC', FB_RSS_PATH . trailingslashit('includes'), true);

define('FB_RSS_VERSION', '1.0');

// Facebook API constants
define('FB_RSS_API_TOKEN', 'Bearer 640010092808045|o-nVSrr-QV02pWtJjhHxdli4r00', true);
define('FB_RSS_API_URL', 'https://graph.facebook.com', true);
define('FB_RSS_API_VERSION', 'v2.4', true);
define('FB_RSS_API_FIELDS', 'id,name,message,picture,type,link', true);
define('FB_RSS_API_MAX_POSTS_DEFAULT', '10', true);