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
        <div class="flex flex-col gap-2">
            <h3 class="text-lg dark:text-gray-400 font-bold ">Votre page personnalisée</h3>
            <a href="?action=profil" class="border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700 text-center font-bold dark:text-gray-400">Accedez à votre profil</a><br><br
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