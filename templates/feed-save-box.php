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
            <input class="button button-primary button-large right" type="submit" name="info_update"
                   value="<?php _e('Save', 'fb_rss'); ?>"/>
            <input class="button button-large" type="submit" name="info_update"
                   value="<?php _e('Save and import', "fb_rss"); ?>" id="save_and_import"/>
        </div>
    </div>
</div>
<?php if ($this->options['imports'] > 10) : ?>
    <div class="rate-box">
        <h4><?php printf(__('%d posts imported and counting!', "fb_rss"), $this->options['imports']); ?></h4>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>
        <i class="icon-star"></i>

        <p class="description"><a href="http://wordpress.org/plugins/rss-post-importer/" target="_blank">Please support
                this plugin by rating it!</a></p>
    </div>
<?php endif; ?>

<!--End of Feedback Box-->

<!--Perfect Audience Start-->
<script type="text/javascript">
    (function () {
        window._pa = window._pa || {};
        // _pa.orderId = "myOrderId"; // OPTIONAL: attach unique conversion identifier to conversions
        // _pa.revenue = "19.99"; // OPTIONAL: attach dynamic purchase values to conversions
        // _pa.productId = "myProductId"; // OPTIONAL: Include product ID for use with dynamic ads
        var pa = document.createElement('script');
        pa.type = 'text/javascript';
        pa.async = true;
        pa.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + "//tag.perfectaudience.com/serve/52c8aa7b965728ddac000007.js";
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(pa, s);
    })();
</script>
<!--Perfect Audience End-->