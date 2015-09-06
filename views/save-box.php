<div class="postbox">
    <div class="inside">
        <div class="misc-pub-section">
            <h3 class="version">V. <?php echo FB_RSS_VERSION; ?></h3>
            <ul>
                <li>
                    <i class="icon-calendar"></i> <?php _e("Latest import:", 'fb_rss'); ?>
                    <strong><?php echo $this->options['latest_import'] ? $this->options['latest_import'] : 'never'; ?></strong>
                </li>
            </ul>
        </div>
        <div id="major-publishing-actions">
            <input class="button button-primary button-large right" type="submit" name="form_submission"
                   value="<?php _e('Import', 'fb_rss'); ?>"/>
            <input class="button button-large" type="submit" name="form_submission"
                   value="<?php _e('Save and import', "fb_rss"); ?>" id="save_and_import"/>
        </div>
    </div>
</div>

<!--End of Feedback Box-->