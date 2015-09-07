<?php

namespace Admin;

use Controllers\FormController;
use Repositories\PostRepository;

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

        add_filter('plugin_action_links_' . FB_RSS_BASENAME, [$this, 'settings_link']);

        // Class instantiation
        $this->formController = new FormController(new PostRepository);

        // add to admin menu
        add_action('admin_menu', [$this, 'admin_menu']);

        // handler form submissions in settings page
        add_action('load-settings_page_fb_rss', [$this->formController, 'handle']);

        // load scripts and styles we need
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * Adds a settings link
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
     * Add to admin menu
     */
    function admin_menu()
    {
        add_options_page('Facebook Rss To Post', 'Facebook Rss To Post', 'manage_options', 'fb_rss', [$this, 'render']);
    }

    /**
     * Display the screen/ui
     */
    function render()
    {
        // include the template for the ui
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
        // don't load if it isn't Facebook RSS screen
        if ($hook != 'settings_page_fb_rss') {
            return;
        }

        // register CSS
        wp_enqueue_style('fb_rss', FB_RSS_URL . 'assets/css/style.css', [], FB_RSS_VERSION);
    }
}
