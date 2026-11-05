<?php

namespace touiteur\action;

use touiteur\action\Action;
use touiteur\render\TouiteRendererCompact;
use touiteur\repository\TouiteRepository;

class DisplayTouitesOfUserAction extends Action
{

    public function execute(): string
    {
        $repo = TouiteRepository::getInstance();
        $user = $repo->getUser(trim($_POST['username']));
        $username = $user->getUsername();

        $touites = $repo->getAllTouites();

        $html = "<section class='touite-action'>";
        $html .= "<h2>Touites de ".$username."</h2>";


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