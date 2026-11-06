<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class DislikeAction extends Action
{
    public function execute(): string
    {
        $repo = TouiteRepository::getInstance();
        $touiteId = $_GET['touiteId'];
        $repo->dislikeTouite($touiteId);

        header("Location: ?action=display-touite&id={$touiteId}");
        exit();
    }
}