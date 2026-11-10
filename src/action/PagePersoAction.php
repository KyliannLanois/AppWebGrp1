<?php

namespace touiteur\action;

use touiteur\render\TouiteRendererCompact;
use touiteur\repository\TouiteRepository;

class PagePersoAction extends Action
{
    public function execute(): string
    {
        $repo = TouiteRepository::getInstance();
        $userName = $_SESSION["user"];
        $user = $repo->getUser($userName);
        $userId = $user->getIdUser();

        $touites = $repo->getTouiteAbonnement($userId);

        $html = <<<HTML
        <div>
            <h3>Votre page personnalisée</h3>
            <a href="?action=profil">Accedez à votre profil</a><br><br
        </div>
HTML;

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