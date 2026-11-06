<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class LikeAction extends Action
{
    public function execute(): string
    {
        $repo = TouiteRepository::getInstance();
        $touiteId = $_GET['touiteId'];

        if (!isset($_SESSION['user'])) {
            header("Location: ?action=display-touite&id={$touiteId}");
            exit();
        }

        $userId = $repo->getUser($_SESSION['user'])->getIdUser();


        $repo->likeTouite($touiteId, $userId);

        header("Location: ?action=display-touite&id={$touiteId}");
        exit();
    }
}