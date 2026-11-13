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

        $html = "<h2 class='text-xl dark:text-gray-400 font-bold'>Bienvenue ".$userName."</h2>";
        $html .= "<section class='flex flex-col gap-2'>";
        $html .= "<a href='?action=afficherAbonnee' class='text-lg text-blue-500 hover:text-blue-600 border rounded p-2 hover:bg-blue-100 font-bold dark:hover:bg-gray-800'>Afficher la liste de ses abonnées</a>";
        $html .= "<h3 class='text-lg dark:text-gray-400 font-bold'>Voici vos touites :</h3>";



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
<p class="text-md dark:text-gray-400">Votre score total est de : {$user->getScore()}</p>
</section>
<section class="flex flex-col gap-2 w-34">
<a href="?action=deconnexion" class="border rounded p-2 hover:bg-gray-300 dark:text-gray-400 dark:hover:bg-gray-300">Se déconnecter</a>
</section>
HTML;

        return $html;
    }
}