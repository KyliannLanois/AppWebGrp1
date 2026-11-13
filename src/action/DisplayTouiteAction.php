<?php

namespace touiteur\action;

use touiteur\render\TouiteRendererCompact;
use touiteur\render\TouiteRendererLong;
use touiteur\repository\TouiteRepository;

/**
 * Action chargée d’afficher un touite spécifique à partir de son identifiant.
 *
 * Récupère le touite demandé via le dépôt, le confie à un moteur de rendu détaillé,
 * puis retourne le contenu HTML généré pour son affichage.
 */
class DisplayTouiteAction extends Action{


    public function execute(): string{
        $repo = TouiteRepository::getInstance();

        $touite = $repo->getTouiteById((int) $_GET["id"]);
        $renderer = new TouiteRendererLong($touite,$repo);
        $html = "";
        $html .= $renderer->render();
        $html .= "</section>";
        return $html;
    }
}