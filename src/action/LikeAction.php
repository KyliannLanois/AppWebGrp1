<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class LikeAction extends Action
{
    public function execute(): string
    {
        $repo = TouiteRepository::getInstance();
        $touiteId = $_GET['touiteId'];
        $userId = $_GET['userId'];
        $repo->likeTouite($touiteId, $userId);

        header("Location: ?action=display-touite&id={$touiteId}");
        exit();
    }
}