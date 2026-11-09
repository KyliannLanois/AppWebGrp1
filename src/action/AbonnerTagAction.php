<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class AbonnerTagAction extends Action
{
    public function execute(): string
    {
        $tagId = $_GET['tagId'];

        $repo = TouiteRepository::getInstance();

        if(isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $userId = $repo->getUser($user)->getIdUser();

            $repo->abonnerTag($userId, $tagId);
        }

        header("Location: ?action=tagPage&id={$tagId}");
        exit();
    }
}