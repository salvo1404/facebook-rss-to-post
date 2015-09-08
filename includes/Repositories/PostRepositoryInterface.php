<?php

namespace includes\Repositories;

interface PostRepositoryInterface
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
    public function createPostsFromJson($json);

    /**
     * This function initialises a new Post
     *
     * @param $item
     *
     * @return mixed
     */
    public function createPost($item);
}
