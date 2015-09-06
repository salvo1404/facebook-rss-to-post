<table class="widefat fb_rss-table" id="fb_rss-settings-table">
    <thead>
    <tr>
        <th colspan="5"><?php _e('Import from Json File', 'fb_rss'); ?></th>
    </tr>
    </thead>
    <tbody class="setting-rows">
    <tr class="edit-row show">
        <td colspan="4">
            <table class="widefat edit-table">
                <tr>
                    <td>
                        <?php _e('Import your JSON file with your feeds', "fb_rss"); ?>
                        <p class="description"><?php _e(
                                'Create and Import a JSON file with your Feeds',
                                "fb_rss"
                            ); ?></p>
                    </td>
                    <td>
                        <input type="file" name="import_json">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>