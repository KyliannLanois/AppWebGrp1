<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class LikeAction extends Action
{
    public function execute(): string
    {
        $repo = TouiteRepository::getInstance();
        $touiteId = $_GET['touiteId'];
        $repo->likeTouite($touiteId);

        header("Location: ?action=display-touite&id={$touiteId}");
        exit();
    }
}