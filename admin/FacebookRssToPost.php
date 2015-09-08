<?php

namespace admin;

use includes\Controllers\FormController;
use includes\Repositories\PostRepository;
use includes\Validators\Validator;

/**
 * Main Class
 *
 */
class FacebookRssToPost
{
    /**
     * A var to store the options in
     *
     * @var array
     */
    public $options;

    /**
     * A var to store the link to the plugin page
     *
     * @var string
     */
    public $page_link;

    /**
     * Start and Initialise
     *
     */
    function __construct()
    {
        // setup this plugin options page link
        $this->page_link = admin_url('options-general.php?page=fb_rss');

        // Add setting link
        add_filter('plugin_action_links_' . FB_RSS_BASENAME, [$this, 'settings_link']);

        // Slow Connection Timeout issue workaround
        add_filter('http_response_timeout', [$this, 'wpdocs_http_response_timeout']);

        // Class instantiation
        $this->formController = new FormController(new PostRepository, new Validator);

        // add to admin menu
        add_action('admin_menu', [$this, 'admin_menu']);

        // handler Settings Page Form Submissions
        add_action('load-settings_page_fb_rss', [$this->formController, 'handle']);

        // load CSS style
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * This function adds a settings link
     *
     * @param array $links Existing links
     *
     * @return type
     */
    public function settings_link($links)
    {
        $settings_link = [
            '<a href="' . $this->page_link . '">Settings</a>',
        ];

        return array_merge($settings_link, $links);
    }

    /**
     * This function resolves the issue of Timeout being reached during Facebook API calls
     * for slow connections
     *
     * @param $timeout
     *
     * @return int
     */
    public function wpdocs_extend_http_response_timeout($timeout)
    {
        return 10;
    }

    /**
     * Add to admin menu
     */
    function admin_menu()
    {
        add_options_page('Facebook Rss To Post', 'Facebook Rss To Post', 'manage_options', 'fb_rss', [$this, 'render']);
    }

    /**
     * This functions displays the screen/ui Settings Page
     */
    function render()
    {
        include(FB_RSS_PATH . '/views/index.php');
    }

    /**
     * Enqueue css
     *
     * @param string $hook The current screens hook
     *
     * @return null
     */
    public function enqueue($hook)
    {
        // don't load if it isn't Facebook RSS to Post screen
        if ($hook != 'settings_page_fb_rss') {
            return;
        }

        wp_enqueue_style('fb_rss', FB_RSS_URL . 'assets/css/style.css', [], FB_RSS_VERSION);
    }
}
