<?php

namespace includes\Repositories;

class PostRepository implements PostRepositoryInterface
{
    /**
     * This function parses the given json object
     * and creates one post per item.
     * Items need to be wrapped
     * into a data namespace.
     *
     * @param $json
     *
     * @return int
     */
    public function createPostsFromJson($json)
    {
        $postCreated = 0;
        foreach ($json->data as $item) {
            if ($this->createPost($item)) {
                $postCreated += 1;
            }
        }

        return $postCreated;
    }

    /**
     * This function initialises a new Post
     * The post will be created only if
     * type = link, video, photo
     *
     * @param $item
     *
     * @return mixed
     */
    public function createPost($item)
    {
        if (!isset($item->type)) {
            return false;
        }

        switch ($item->type) {
            case 'link':
                return $this->createLinkPost($item);
            case 'photo':
                return $this->createPhotoPost($item);
            case 'video':
                return $this->createVideoPost($item);
        }

    }

    /**
     * This function creates a link Post
     *
     * @param $item
     *
     * @return bool
     */
    private function createLinkPost($item)
    {
        $wp_values                 = [];
        $wp_values['post_content'] = '';

        if (!isset($item->name)) {
            $wp_values['post_title'] = 'Untitled';
        } else {
            $wp_values['post_title'] = $item->name;
        }

        if (isset($item->message)) {
            $wp_values['post_content'] = $item->message . ' ' .
                '<br><br><a href="' . $item->link . '"><strong>READ MORE</strong></a>';
        }

        $wp_values['post_status'] = 'publish';
        $wp_values['post_type']   = 'post';

        if (isset($item->picture)) {
            $pictureLocalPath = download_url($item->picture);
            if (!is_wp_error($pictureLocalPath)) {
                $wp_values['post_content'] = $this->addImageToPost($pictureLocalPath, $wp_values['post_content']);
            }
        }

        if (!$postId = $this->save($wp_values)) {
            return false;
        }

        return $postId;
    }

    /**
     * This function creates a photo Post
     *
     * @param $item
     *
     * @return bool
     */
    private function createPhotoPost($item)
    {
        $wp_values                 = [];
        $wp_values['post_content'] = '';

        if (!isset($item->name)) {
            $wp_values['post_title'] = 'Untitled - Photo';
        } else {
            $wp_values['post_title'] = $item->name;
        }

        if (isset($item->message)) {
            $wp_values['post_content'] = $item->message;
        }

        $wp_values['post_status'] = 'publish';
        $wp_values['post_type']   = 'post';

        if (isset($item->picture)) {
            $pictureLocalPath = download_url($item->picture);
            if (!is_wp_error($pictureLocalPath)) {
                $wp_values['post_content'] = $this->addImageToPost($pictureLocalPath, $wp_values['post_content']);
            }
        }

        if (!$postId = $this->save($wp_values)) {
            return false;
        }

        return $postId;
    }

    /**
     * This function creates a video Post
     *
     * @param $item
     *
     * @return bool
     */
    private function createVideoPost($item)
    {
        $wp_values                 = [];
        $wp_values['post_content'] = '';

        if (!isset($item->name)) {
            $wp_values['post_title'] = 'Untitled - Video';
        } else {
            $wp_values['post_title'] = $item->name;
        }

        if (isset($item->message)) {
            $wp_values['post_content'] = $item->message;
        }

        if (isset($item->link)) {
            $wp_values['post_content'] = $wp_values['post_content'] . ' ' .
                '<br><br><a href="' . $item->link . '"><strong>WATCH VIDEO</strong></a>';
        }

        $wp_values['post_status'] = 'publish';
        $wp_values['post_type']   = 'post';

        if (isset($item->picture)) {
            $pictureLocalPath = download_url($item->picture);
            if (!is_wp_error($pictureLocalPath)) {
                $wp_values['post_content'] = $this->addImageToPost($pictureLocalPath, $wp_values['post_content']);
            }
        }

        if (!$postId = $this->save($wp_values)) {
            return false;
        }

        return $postId;
    }

    /**
     * This function adds an Image to Post Content
     *
     * @param $imageLocalPath
     * @param $postContent
     *
     * @return string
     */
    private function addImageToPost($imageLocalPath, $postContent)
    {
        $fileUploaded = wp_upload_bits('attachment.jpg', null, file_get_contents($imageLocalPath));

        if (!($fileUploaded['error'])) {
            $imageDisplay = '<img src="' . $fileUploaded['url'] . '" class="attachment-post-thumbnail wp-post-image" alt>';
            $postContent  = $imageDisplay . '<br><br />' . $postContent;
        }

        return $postContent;
    }

    /**
     * This function saves the new Post into the DB
     *
     * @param $wp_values
     *
     * @return bool
     */
    private function save($wp_values)
    {
        $postId = wp_insert_post($wp_values);

        if (!$postId || is_wp_error($postId)) {
            return false;
        }

        return $postId;
    }

}
