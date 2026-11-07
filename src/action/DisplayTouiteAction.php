<?php

namespace touiteur\action;

use touiteur\render\TouiteRendererCompact;
use touiteur\render\TouiteRendererLong;
use touiteur\repository\TouiteRepository;

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