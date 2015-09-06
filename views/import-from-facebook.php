<table class="widefat fb_rss-table" id="fb_rss-feed-table">
    <thead>
    <tr>
        <th colspan="5"><?php _e('Import from Facebook', 'fb_rss'); ?></th>
    </tr>
    </thead>
    <thead>
    <tr>
        <th><?php _e("Feed name", 'fb_rss'); ?></th>
        <th><?php _e("Feed url", 'fb_rss'); ?></th>
        <th><?php _e("Max posts / import", 'fb_rss'); ?></th>
    </tr>
    </thead>
    <tbody class="rss-rows">
    <?php
    $saved_ids = [];

    if (is_array($this->options['feeds']) && count($this->options['feeds']) > 0) :
        foreach ($this->options['feeds'] as $f) :
            $category = get_the_category($f['category_id']);
            array_push($saved_ids, $f['id']);
            include(FB_RSS_PATH . '/views/feed-table-row.php');
        endforeach;
    else :
        ?>
        <tr>
            <td colspan="4" class="empty_table">
                None
            </td>
        </tr>
        <?php
    endif
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4">
            <a href="#" class="button button-large button-primary add-row">
                <?php _e('Add new feed', "fb_rss"); ?>
            </a>
            <input type="hidden" name="ids" id="ids" value="<?php echo(join($saved_ids, ',')); ?>"/>
        </td>
    </tr>
    <?php
    // preload an empty (and hidden by css) "new feed" row
    unset($f);
    include(FB_RSS_PATH . '/views/feed-table-row.php');
    ?>
    </tfoot>
</table>

<style>
    .fb_rss-table tfoot tr.data-row, .fb_rss-table tfoot tr.edit-row {
        display: none;
    }
</style>