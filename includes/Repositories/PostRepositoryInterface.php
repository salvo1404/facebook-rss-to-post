<?php

namespace Repositories;

interface PostRepositoryInterface
{
    /**
     * @param $json
     *
     * @return int
     */
    public function createPostsFromJson($json);

}
