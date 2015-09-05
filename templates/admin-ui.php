<div class="wrap">
    <div id="main_ui">

        <h2><?php _e("Facebook Rss To Post Settings", 'fb_rss'); ?></h2>

        <div id="fb_rss_progressbar"></div>
        <div id="fb_rss_progressbar_label"></div>

        <form method="post" id="fb_rss-settings-form" enctype="multipart/form-data"
              action="<?php echo $fb_rss_to_post->page_link; ?>">

            <input type="hidden" name="save_to_db" id="save_to_db"/>

            <?php wp_nonce_field('settings_page', 'fb_rss_nonce'); ?>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="postbox-container-1" class="postbox-container">
                        <?php include_once FB_RSS_PATH . 'templates/feed-save-box.php'; ?>
                    </div>

                    <div id="postbox-container-2" class="postbox-container">
                        <?php
                        include_once FB_RSS_PATH . 'templates/feed-table.php';
                        include_once FB_RSS_PATH . 'templates/settings-table.php';
                        ?>
                    </div>

                </div>
                <br class="clear"/>
            </div>
        </form>

    </div>
    <div class="ajax_content"></div>
</div>