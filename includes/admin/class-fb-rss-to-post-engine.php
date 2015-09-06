<?php

class FbRssToPostEngine
{

    // Note:
    //   JSON_API_Post objects must be instantiated within The Loop.

    var $id;              // Integer
    var $type;            // String
    var $slug;            // String
    var $url;             // String
    var $status;          // String ("draft", "published", or "pending")
    var $title;           // String
    var $title_plain;     // String
    var $content;         // String (modified by read_more query var)
    var $excerpt;         // String
    var $date;            // String (modified by date_format query var)
    var $modified;        // String (modified by date_format query var)
    var $categories;      // Array of objects
    var $tags;            // Array of objects
    var $author;          // Object
    var $comments;        // Array of objects
    var $attachments;     // Array of objects
    var $comment_count;   // Integer
    var $comment_status;  // String ("open" or "closed")
    var $thumbnail;       // String
    var $custom_fields;   // Object (included by using custom_fields query var)

    /**
     * @param $json
     *
     * @return int
     */
    public function createPostsFromJson($json)
    {
        // apply some data validation

        $count = 0;
        foreach ($json->data as $dataItem) {
            $this->createPost($dataItem);
            $count += 1;
        }

        return $count;
    }

    /**
     * @param $item
     *
     * @return mixed
     */
    private function createPost($item)
    {
        unset($item->id);

        if (empty($item) || !isset($item->name)) {
            $item = [
                'title'   => 'Untitled',
                'content' => ''
            ];
        }

        return $this->savePost($item);
    }

    private function savePost($item)
    {
        $wp_values = [];

        if (isset($item->id)) {
            $wp_values['ID'] = $item->id;
        }

        if (isset($item->message)) {
            $wp_values['post_content'] = $item->message;
        }

        if(isset($item->type)){
            if($item->type === 'link'){

            }
        }

        if (isset($item->name)) {
            $wp_values['post_title'] = $item->name;
        }

        $wp_values['post_type'] = 'post';

        $wp_values['post_status'] = 'publish';

        if (isset($wp_values['ID'])) {
            $this->id = wp_update_post($wp_values);
        } else {
            $this->id = wp_insert_post($wp_values);
        }

        //$wp_post = get_post($this->id);
        //$this->import_wp_object($wp_post);

        return $this->id;
    }

    function updatePost($data)
    {
        $data['id'] = $this->id;

        return $this->savePost($data);
    }

    function import_wp_object($wp_post)
    {
        global $json_api, $post;
        $date_format = $json_api->query->date_format;
        $this->id    = (int)$wp_post->ID;
        setup_postdata($wp_post);
        $this->set_value('type', $wp_post->post_type);
        $this->set_value('slug', $wp_post->post_name);
        $this->set_value('url', get_permalink($this->id));
        $this->set_value('status', $wp_post->post_status);
        $this->set_value('title', get_the_title($this->id));
        $this->set_value('title_plain', strip_tags(@$this->title));
        $this->set_content_value();
        $this->set_value('excerpt', apply_filters('the_excerpt', get_the_excerpt()));
        $this->set_value('date', get_the_time($date_format));
        $this->set_value('modified', date($date_format, strtotime($wp_post->post_modified)));
        $this->set_categories_value();
        $this->set_tags_value();
        $this->set_author_value($wp_post->post_author);
        $this->set_comments_value();
        $this->set_attachments_value();
        $this->set_value('comment_count', (int)$wp_post->comment_count);
        $this->set_value('comment_status', $wp_post->comment_status);
        $this->set_thumbnail_value();
        $this->set_custom_fields_value();
        $this->set_custom_taxonomies($wp_post->post_type);
        do_action("json_api_import_wp_post", $this, $wp_post);
    }

    function set_value($key, $value)
    {
        global $json_api;
        if ($json_api->include_value($key)) {
            $this->$key = $value;
        } else {
            unset($this->$key);
        }
    }

    function set_content_value()
    {
        global $json_api;
        if ($json_api->include_value('content')) {
            $content       = get_the_content($json_api->query->read_more);
            $content       = apply_filters('the_content', $content);
            $content       = str_replace(']]>', ']]&gt;', $content);
            $this->content = $content;
        } else {
            unset($this->content);
        }
    }

    function set_categories_value()
    {
        global $json_api;
        if ($json_api->include_value('categories')) {
            $this->categories = [];
            if ($wp_categories = get_the_category($this->id)) {
                foreach ($wp_categories as $wp_category) {
                    $category = new JSON_API_Category($wp_category);
                    if ($category->id == 1 && $category->slug == 'uncategorized') {
                        // Skip the 'uncategorized' category
                        continue;
                    }
                    $this->categories[] = $category;
                }
            }
        } else {
            unset($this->categories);
        }
    }

    function set_tags_value()
    {
        global $json_api;
        if ($json_api->include_value('tags')) {
            $this->tags = [];
            if ($wp_tags = get_the_tags($this->id)) {
                foreach ($wp_tags as $wp_tag) {
                    $this->tags[] = new JSON_API_Tag($wp_tag);
                }
            }
        } else {
            unset($this->tags);
        }
    }

    function set_author_value($author_id)
    {
        global $json_api;
        if ($json_api->include_value('author')) {
            $this->author = new JSON_API_Author($author_id);
        } else {
            unset($this->author);
        }
    }

    function set_comments_value()
    {
        global $json_api;
        if ($json_api->include_value('comments')) {
            $this->comments = $json_api->introspector->get_comments($this->id);
        } else {
            unset($this->comments);
        }
    }

    function set_attachments_value()
    {
        global $json_api;
        if ($json_api->include_value('attachments')) {
            $this->attachments = $json_api->introspector->get_attachments($this->id);
        } else {
            unset($this->attachments);
        }
    }

    function set_thumbnail_value()
    {
        global $json_api;
        if (!$json_api->include_value('thumbnail') ||
            !function_exists('get_post_thumbnail_id')
        ) {
            unset($this->thumbnail);

            return;
        }
        $attachment_id = get_post_thumbnail_id($this->id);
        if (!$attachment_id) {
            unset($this->thumbnail);

            return;
        }
        $thumbnail_size         = $this->get_thumbnail_size();
        $this->thumbnail_size   = $thumbnail_size;
        $attachment             = $json_api->introspector->get_attachment($attachment_id);
        $image                  = $attachment->images[$thumbnail_size];
        $this->thumbnail        = $image->url;
        $this->thumbnail_images = $attachment->images;
    }

    function set_custom_fields_value()
    {
        global $json_api;
        if ($json_api->include_value('custom_fields')) {
            $wp_custom_fields    = get_post_custom($this->id);
            $this->custom_fields = new stdClass();
            if ($json_api->query->custom_fields) {
                $keys = explode(',', $json_api->query->custom_fields);
            }
            foreach ($wp_custom_fields as $key => $value) {
                if ($json_api->query->custom_fields) {
                    if (in_array($key, $keys)) {
                        $this->custom_fields->$key = $wp_custom_fields[$key];
                    }
                } else {
                    if (substr($key, 0, 1) != '_') {
                        $this->custom_fields->$key = $wp_custom_fields[$key];
                    }
                }
            }
        } else {
            unset($this->custom_fields);
        }
    }

    function set_custom_taxonomies($type)
    {
        global $json_api;
        $taxonomies = get_taxonomies(
            [
                'object_type' => [$type],
                'public'      => true,
                '_builtin'    => false
            ],
            'objects'
        );
        foreach ($taxonomies as $taxonomy_id => $taxonomy) {
            $taxonomy_key = "taxonomy_$taxonomy_id";
            if (!$json_api->include_value($taxonomy_key)) {
                continue;
            }
            $taxonomy_class      = $taxonomy->hierarchical ? 'JSON_API_Category' : 'JSON_API_Tag';
            $terms               = get_the_terms($this->id, $taxonomy_id);
            $this->$taxonomy_key = [];
            if (!empty($terms)) {
                $taxonomy_terms = [];
                foreach ($terms as $term) {
                    $taxonomy_terms[] = new $taxonomy_class($term);
                }
                $this->$taxonomy_key = $taxonomy_terms;
            }
        }
    }

    function get_thumbnail_size()
    {
        global $json_api;
        if ($json_api->query->thumbnail_size) {
            return $json_api->query->thumbnail_size;
        } else {
            if (function_exists('get_intermediate_image_sizes')) {
                $sizes = get_intermediate_image_sizes();
                if (in_array('post-thumbnail', $sizes)) {
                    return 'post-thumbnail';
                }
            }
        }

        return 'thumbnail';
    }

}
