<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;
use touiteur\render\TouiteRendererCompact;

class HomeAction extends Action {

    public function execute(): string{
        $repo = TouiteRepository::getInstance();
        $touites = $repo->getAllTouites();

        $html = "<section class='touite-action'>";
        $html .= "<h2>Derniers touites publiés</h2>";


        if (empty($touites)) {
            $html .= "<p> Aucun touite pour le moment</p>";
        } else {
            foreach($touites as $touite){
                $renderer = new TouiteRendererCompact($touite,$repo);
                $html .= $renderer->render();
            }
        }

        $html .= "</section>";
        return $html;
    }


}