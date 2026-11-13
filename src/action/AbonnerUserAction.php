<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

/**
 * Action permettant à l’utilisateur connecté de s’abonner à un autre utilisateur.
 *
 * Récupère l’identifiant de l’utilisateur à suivre depuis la requête, vérifie
 * l’existence d’un utilisateur en session, puis enregistre l’abonnement via le dépôt.
 * Redirige ensuite vers le touite d’origine lorsqu’il est indiqué, ou vers la page
 * des touites de l’utilisateur suivi.
 */
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