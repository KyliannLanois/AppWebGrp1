<?php

namespace touiteur\action;

use touiteur\render\TouiteRendererCompact;
use touiteur\repository\TouiteRepository;

class DisplayTouiteAction extends Action{


    public function execute(): string{
        $repo = TouiteRepository::getInstance();

        $touite = $_GET["id"];

        if (empty($touites)) {
            $html .= "<p> Aucun touite pour le moment</p>";
        } else {
            foreach($touites as $touite){
                $renderer = new TouiteRendererCompact($touite);
                $html .= $renderer->render();
            }
        }

        $html .= "</section>";
        return $html;
    }
}