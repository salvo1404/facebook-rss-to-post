<?php

namespace includes\Repositories;

interface PostRepositoryInterface
{
    /**
     * @param $json
     *
     * @return int
     */
    public function createPostsFromJson($json);

}
