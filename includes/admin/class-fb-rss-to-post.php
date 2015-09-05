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
    public $options = [];

    /**
     * A var to store the link to the plugin page
     *
     * @var string
     */
    public $page_link;

    /**
     * To initialise the admin and cron classes
     *
     * @var object
     */
    private $admin, $cron;

    /**
     * Start
     */
    function __construct()
    {

        // populate the options first
        $this->load_options();

        // setup this plugin options page link
        $this->page_link = admin_url('options-general.php?page=fb_rss');

        // hook translations
        add_action('plugins_loaded', [$this, 'localize']);

        add_filter('plugin_action_links_' . FB_RSS_BASENAME, [$this, 'settings_link']);
    }

    /**
     * Load options from the db
     */
    public function load_options()
    {
        $default_settings = [
            'enable_logging'        => true,
            'feeds_api_key'         => false,
            'frequency'             => 0,
            'post_template'         => "{\$content}\nSource: {\$feed_title}",
            'post_status'           => 'publish',
            'author_id'             => 1,
            'allow_comments'        => 'open',
            'block_indexing'        => false,
            'nofollow_outbound'     => true,
            'keywords'              => [],
            'import_images_locally' => false,
            'disable_thumbnail'     => false,
            'cache_deleted'         => true,
        ];

        $options = get_option('rss_pi_feeds', []);

        // prepare default options when there is no record in the database
        if (!isset($options['feeds'])) {
            $options['feeds'] = [];
        }
        if (!isset($options['settings'])) {
            $options['settings'] = [];
        }
        if (!isset($options['latest_import'])) {
            $options['latest_import'] = '';
        }
        if (!isset($options['imports'])) {
            $options['imports'] = 0;
        }
        if (!isset($options['upgraded'])) {
            $options['upgraded'] = [];
        }

        $options['settings'] = wp_parse_args($options['settings'], $default_settings);

        if (!array_key_exists('imports', $options)) {
            $options['imports'] = 0;
        }

        $this->options = $options;
    }

    /**
     * Load translations
     */
    public function localize()
    {
        load_plugin_textdomain('fb_rss', false, FB_RSS_PATH . 'lang/');
    }

    /**
     * Initialise
     */
    public function init()
    {
        // add to admin menu
        add_action('admin_menu', [$this, 'admin_menu']);

        // load scripts and styles we need
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));

        /*// initialise admin and cron
        $this->cron = new rssPICron();
        $this->cron->init();

        $this->admin = new rssPIAdmin();
        $this->admin->init();

        $this->front = new rssPIFront();
        $this->front->init();*/
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
        add_options_page('Facebook Rss To Post', 'Facebook Rss To Post', 'manage_options', 'fb_rss', [$this, 'screen']);
    }

    /**
     * Display the screen/ui
     */
    function screen()
    {
        // it'll process any submitted form data
        // reload the options just in case
        $this->load_options();

        // include the template for the ui
        include(FB_RSS_PATH . 'templates/admin-ui.php');
    }

    /**
     * Enqueue our admin css and js
     *
     * @param string $hook The current screens hook
     * @return null
     */
    public function enqueue($hook) {

        // don't load if it isn't Facebook RSS screen
        if ($hook != 'settings_page_fb_rss') {
            return;
        }

        // register scripts & styles
        wp_enqueue_style('fb_rss', FB_RSS_URL . 'assets/css/style.css', array(), FB_RSS_VERSION);

        wp_enqueue_style('fb_rss-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/redmond/jquery-ui.css', array(), FB_RSS_VERSION);

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-progressbar');

        wp_enqueue_script('modernizr', FB_RSS_URL . 'assets/js/modernizr.custom.32882.js', array(), FB_RSS_VERSION, true);
        wp_enqueue_script('phpjs-uniqid', FB_RSS_URL . 'assets/js/uniqid.js', array(), FB_RSS_VERSION, true);
        wp_enqueue_script('fb_rss', FB_RSS_URL . 'assets/js/main.js', array('jquery'), FB_RSS_VERSION, true);

        // localise ajaxuel for use
        $localise_args = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'pluginurl' => FB_RSS_URL,
            'l18n' => array(
                'unsaved' => __( 'You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?', 'fb_rss' )
            )
        );
        wp_localize_script('fb_rss', 'fb_rss', $localise_args);
    }
}
