<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class AbonnerUserAction extends Action
{
    public function execute(): string
    {
        $idFollowed = $_GET['idFollowed'];

        $repo = TouiteRepository::getInstance();

        if(isset($_SESSION['user'])){
            $follower = $_SESSION['user'];
            $idFollower = $repo->getUser($follower)->getIdUser();

            $repo->abonnerUser($idFollower, $idFollowed);
        }
        if(isset($_GET['touiteId'])){
            $touiteId = $_GET['touiteId'];
            header("Location: ?action=display-touite&id={$touiteId}");
            exit();
        } else {
            header("Location: ?action=DisplayTouiteOfUser&id={$idFollowed}");
            exit();
        }
    }
}