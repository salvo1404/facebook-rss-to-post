<?php
/*
Plugin Name:       Facebook RSS to Post
Plugin URI:        https://github.com/salvo1404/facebook-rss-to-post
Description:       This plugin allows you to import RSS and Atom feeds from your favourite Facebook page and transform items to Wordpress's posts.
Version:           1.0.0
Author:            Salvatore Balzano
Author URI:        https://github.com/salvo1404
License:           GPLv2 or later
*/

use admin\FacebookRssToPost;

// Includes
include_once('constants.php');
include_once('admin/FacebookRssToPost.php');
include_once(FB_RSS_INC . '/Controllers/FormController.php');
include_once(FB_RSS_INC . '/Repositories/PostRepositoryInterface.php');
include_once(FB_RSS_INC . '/Repositories/PostRepository.php');
include_once(FB_RSS_INC . '/Validators/Validator.php');

$fb_rss_to_post = new FacebookRssToPost;
