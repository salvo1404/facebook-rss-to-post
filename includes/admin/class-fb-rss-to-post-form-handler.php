<?php

/**
 * Processes the admin screen form submissions
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */
class FbRssToPostFormHandler
{
    /**
     * @var FbRssToPostEngine
     */
    private $engine;

    /**
     * @param FbRssToPostEngine $engine
     */
    function __construct(FbRssToPostEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * This function handles form submissions
     *
     */
    function handle()
    {
        // if there's nothing to process or invalid nonce
        if (!isset($_POST['form_submission']) || !wp_verify_nonce($_POST['fb_rss_nonce'], 'settings_page')) {
            printf('posts imported and counting!ddddddddddddddddd', "fb_rss");

            return;
        }

        // import JSON file
        if (isset($_FILES['import_json']) && is_uploaded_file($_FILES['import_json']['tmp_name'])) {
            $file = file_get_contents($_FILES['import_json']['tmp_name']);
            $json = json_decode($file);

            @unlink($file);

            // apply some json file validation:

            $postImported = $this->engine->createPostsFromJson($json);

            $this->printMessage($postImported);

            //$feeds = $this->_parse_data( $json->data, $feeds );
            //$this->options['feeds'] = $feeds;
        }
    }

    /**
     * This function prints a success message after importing
     *
     * @param $postImported
     */
    private function printMessage($postImported)
    {
        ?>
        <div id="message" class="updated">
            <p><strong><?php _e($postImported . ' Posts Imported.') ?></strong></p>
        </div>
        <?php
    }

    /**
     * Forms the feeds array from submitted data
     *
     * @param array $ids feeds ids
     *
     * @return array
     */
    private function process_feeds($ids)
    {

        $feeds = [];

        foreach ($ids as $id) {
            if ($id) {
                $keywords = [];
                // if the key is valid
                if ($this->is_key_valid) {
                    // set up keywords (otherwise don't)
                    if (isset($_POST[$id . '-keywords'])) {
                        $keyword_str = $_POST[$id . '-keywords'];
                    }
                    if (!empty($keyword_str)) {
                        $keywords = explode(',', $keyword_str);
                    }
                }
                array_push(
                    $feeds,
                    [
                        'id'          => $id,
                        'url'         => $_POST[$id . '-url'],
                        'name'        => $_POST[$id . '-name'],
                        'max_posts'   => $_POST[$id . '-max_posts'],
                        // different author ids depending on valid API keys
                        'author_id'   => ($this->is_key_valid && isset($_POST[$id . '-author_id'])) ? $_POST[$id . '-author_id'] : $_POST['author_id'],
                        'category_id' => (isset($_POST[$id . '-category_id'])) ? $_POST[$id . '-category_id'] : '',
                        'tags_id'     => (isset($_POST[$id . '-tags_id'])) ? $_POST[$id . '-tags_id'] : '',
                        'keywords'    => array_map('trim', $keywords),
                        'strip_html'  => (isset($_POST[$id . '-strip_html'])) ? $_POST[$id . '-strip_html'] : ''
                    ]
                );
            }
        }

        return $feeds;
    }

    /**
     * Update options and reload global options
     *
     * @global type $rss_post_importer
     *
     * @param array $settings
     * @param array $feeds
     */
    private function save_reload_options($settings, $feeds)
    {

        global $rss_post_importer;

        // existing options
        $options = $rss_post_importer->options;

        // new data
        $new_options = [
            'feeds'         => $feeds,
            'settings'      => $settings,
            'latest_import' => $options['latest_import'],
            'imports'       => $options['imports'],
            'upgraded'      => $options['upgraded']
        ];

        // update in db
        update_option('rss_pi_feeds', $new_options);

        // reload so that the new options are used henceforth
        $rss_post_importer->load_options();
    }
}