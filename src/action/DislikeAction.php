<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

/**
 * Action chargée d’enregistrer un désaccord pour un touite.
 *
 * Vérifie la présence d’un utilisateur connecté, applique le vote négatif associé au touite demandé,
 * puis redirige vers la page d’affichage de ce touite.
 */
class DislikeAction extends Action
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
        $repo->dislikeTouite($touiteId, $userId);

        header("Location: ?action=display-touite&id={$touiteId}");
        exit();
    }
}