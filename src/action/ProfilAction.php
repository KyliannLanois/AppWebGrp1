<?php

namespace touiteur\action;

use touiteur\render\TouiteRendererCompact;
use touiteur\repository\TouiteRepository;

class ProfilAction extends Action
{
    public function execute(): string
    {

        $repo = TouiteRepository::getInstance();
        $userName = $_SESSION["user"];
        $user = $repo->getUser($userName);
        $userId = $user->getIdUser();

        $touites = $repo->getListTouiteUser($userId);

        $html = "<section class='touite-action'>";
        $html .= "<h2>Bienvenu ".$userName."</h2>";
        $html .= "<h3>Voici vos touites :</h3>";



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