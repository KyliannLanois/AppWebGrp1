<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

/**
 * Action permettant à l’utilisateur connecté de s’abonner à un tag.
 *
 * Récupère l’identifiant du tag depuis la requête, vérifie la présence d’un utilisateur en session,
 * enregistre l’abonnement via le dépôt applicatif, puis redirige vers la page du tag concerné.
 */
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