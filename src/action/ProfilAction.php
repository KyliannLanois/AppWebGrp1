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
        $html .= "<a href='?action=afficherAbonnee'>Afficher la liste de ses abonnées</a>";
        $html .= "<h3>Voici vos touites :</h3>";



        if (empty($touites)) {
            $html .= "<p> Aucun touite pour le moment</p>";
        } else {
            foreach($touites as $touite){
                $renderer = new TouiteRendererCompact($touite,$repo);
                $html .= $renderer->render();
            }
        }

        $html .= "</section>";

        $html .= <<<HTML
<section class="scoreProfil">
<p>Votre score total est de : {$user->getScore()}</p>
</section>
<section class="deconnexion">
<a href="?action=deconnexion">Se déconnecter</a>
</section>
HTML;

        return $html;
    }
}