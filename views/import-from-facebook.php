<table class="widefat fb_rss-table" id="fb_rss-feed-table">
    <thead>
    <tr>
        <th colspan="5"><?php _e('Import from Facebook Page', 'fb_rss'); ?></th>
    </tr>
    </thead>
    <thead>
    <tr>
        <th><?php _e("Facebook Page Name", 'fb_rss'); ?>
            <p class="description"><?php _e(
                    'Insert page name only (e.g. "news.com.au") ',
                    "fb_rss"
                ); ?></p>
        </th>
        <th><?php _e("Max posts (default = 10)", 'fb_rss'); ?>
            <p class="description"><?php _e(
                    'Max number of posts to import order by most recent',
                    "fb_rss"
                ); ?></p>
        </th>
    </tr>
    </thead>
    <tbody class="rss-rows">
    <tr class="import-fb-input">
        <th><input type="text" name="page_name" placeholder="Page Name" required/></th>
        <th><input type="number" name="max_posts" min="1" placeholder="Max Posts"/></th>
    </tr>
    </tbody>

</table>