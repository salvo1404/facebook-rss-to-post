<?php

/**
 * Main Class
 *
 */
class FbRssToPost
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

        // hook translations
        add_action('plugins_loaded', [$this, 'localize']);

        add_filter('plugin_action_links_' . FB_RSS_BASENAME, [$this, 'settings_link']);

        // Class instantiation
        $this->engine  = new FbRssToPostEngine;
        $this->handler = new FbRssToPostFormHandler($this->engine);

        // add to admin menu
        add_action('admin_menu', [$this, 'admin_menu']);

        // handler form submissions in settings page
        add_action('load-settings_page_fb_rss', [$this->handler, 'handle']);

        // load scripts and styles we need
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * Load translations
     */
    public function localize()
    {
        load_plugin_textdomain('fb_rss', false, FB_RSS_PATH . 'lang/');
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
     * Enqueue our admin css and js
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

        // register scripts & styles
        wp_enqueue_style('fb_rss', FB_RSS_URL . 'assets/css/style.css', [], FB_RSS_VERSION);

        wp_enqueue_style(
            'fb_rss-jquery-ui-css',
            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/redmond/jquery-ui.css',
            [],
            FB_RSS_VERSION
        );

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-progressbar');

        wp_enqueue_script('modernizr', FB_RSS_URL . 'assets/js/modernizr.custom.32882.js', [], FB_RSS_VERSION, true);
        wp_enqueue_script('phpjs-uniqid', FB_RSS_URL . 'assets/js/uniqid.js', [], FB_RSS_VERSION, true);
        wp_enqueue_script('fb_rss', FB_RSS_URL . 'assets/js/main.js', ['jquery'], FB_RSS_VERSION, true);

        // localise ajaxuel for use
        $localise_args = [
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'pluginurl' => FB_RSS_URL,
            'l18n'      => [
                'unsaved' => __(
                    'You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?',
                    'fb_rss'
                )
            ]
        ];
        wp_localize_script('fb_rss', 'fb_rss', $localise_args);
    }
}
