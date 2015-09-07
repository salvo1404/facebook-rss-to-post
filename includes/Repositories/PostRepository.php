<?php

class PostRepository implements PostRepositoryInterface
{
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

}
