<?php
$show = '';

if (!isset($f)) {
    $f    = [
        'id'          => 0,
        'name'        => 'New feed',
        'url'         => '',
        'max_posts'   => 5,
        'author_id'   => 1,
        'category_id' => 1,
        'tags_id'     => [],
        'strip_html'  => 'false'
    ];
    $show = ' show';
}

if (is_array($f['tags_id'])) {
    if (!empty($f['tags_id'])) {
        foreach ($f['tags_id'] as $tag) {
            $tagname    = get_tag($tag);
            $tagarray[] = $tagname->name;
        }
        $tag = join(',', $tagarray);
    } else {
        $tag = [];
    }
} else {
    if (empty($f['tags_id'])) {
        $f['tags_id'] = [];
        $tag          = '';
    } else {
        $f['tags_id'] = [$f['tags_id']];
        $tagname      = get_tag(intval($f['tags_id']));
        $tag          = $tagname->name;
    }
}

if (is_array($f['category_id'])) {
    foreach ($f['category_id'] as $cat) {
        $catarray[] = get_cat_name($cat);
    }
    $category = join(',', $catarray);
} else {
    if (empty($f['category_id'])) {
        $f['category_id'] = [1];
        $category         = get_the_category_by_ID(1);
    } else {
        $f['category_id'] = [$f['category_id']];
        $category         = get_the_category_by_ID(intval($f['category_id']));
    }
}
?>

<tr id="display_<?php echo($f['id']); ?>" class="data-row<?php echo $show; ?>" data-fields="name,url,max_posts">
    <td class="rss_pi-feed_name">
        <strong><a href="#" class="toggle-edit" data-target="<?php echo($f['id']); ?>"><span
                    class="field-name"><?php echo esc_html(stripslashes($f['name'])); ?></span></a></strong>

        <div class="row-options">
            <a href="#" class="toggle-edit" data-target="<?php echo($f['id']); ?>"><?php _e('Edit', 'rss_pi'); ?></a> |
            <a href="#" class="delete-row" data-target="<?php echo($f['id']); ?>"><?php _e('Delete', 'rss_pi'); ?></a>
        </div>
    </td>
    <td class="rss_pi-feed_url"><span class="field-url"><?php echo esc_url(stripslashes($f['url'])); ?></span></td>
    <td class="rss_pi_feed_max_posts"><span class="field-max_posts"><?php echo $f['max_posts']; ?></span></td>
    <!-- <td width="20%"><?php //echo $category;  ?></td>-->
</tr>
<tr id="edit_<?php echo($f['id']); ?>" class="edit-row<?php echo $show; ?>">
    <td colspan="4">
        <table class="widefat edit-table">
            <tr>
                <td><label for="<?php echo($f['id']); ?>-name"><?php _e("Feed name", 'rss_pi'); ?></label></td>
                <td>
                    <input type="text" class="field-name" name="<?php echo($f['id']); ?>-name"
                           id="<?php echo($f['id']); ?>-name"
                           value="<?php echo esc_attr(stripslashes($f['name'])); ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo($f['id']); ?>-url"><?php _e("Feed url", 'rss_pi'); ?></label>

                    <p class="description">e.g. "http://news.google.com/?output=rss"</p>
                </td>
                <td><input type="text" class="field-url" name="<?php echo($f['id']); ?>-url"
                           id="<?php echo($f['id']); ?>-url" value="<?php echo esc_attr(stripslashes($f['url'])); ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="<?php echo($f['id']); ?>-max_posts"><?php _e("Max posts / import", 'rss_pi'); ?></label>
                </td>
                <td><input type="number" class="field-max_posts" name="<?php echo($f['id']); ?>-max_posts"
                           id="<?php echo($f['id']); ?>-max_posts" value="<?php echo($f['max_posts']); ?>" min="1"
                           max="100"/></td>
            </tr>
            <tr>
                <td><input type="hidden" name="id" value="<?php echo($f['id']); ?>"/></td>
                <td><a id="close-edit-table-<?php echo($f['id']); ?>" class="button button-large toggle-edit"
                       data-target="<?php echo($f['id']); ?>"><?php _e('Close', 'rss_pi'); ?></a></td>
            </tr>
        </table>

    </td>
</tr>
