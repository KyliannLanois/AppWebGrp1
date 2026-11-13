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
        $iduser = trim($_GET['id']);
        $user = $repo->getUserByID($iduser);


        $username = $user->getUsername();

        if (isset($_GET['ordre']) && $_GET['ordre'] == 'asc') {
            $touites = $repo->getListTouiteUser($iduser);
        } else {
            $touites = $repo->getListTouiteUserInverse($iduser);
        }

        $html = <<<HTML
        <section class='touite-action'>
        <div class="flex justify-between">
        <h2 class="text-lg dark:text-gray-400 font-bold ">Touites de ".$username."</h2>
         <a href="?action=abonnementUser&idFollowed={$iduser}" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">{$repo->estAbonne($iduser)}</a>
        </div>
        
        <br><br>
        <details>
        <summary class="text-lg dark:text-gray-400 font-bold">Trier par</summary>
        <div class="flex item-center gap-2">
        <a href="?action=DisplayTouiteOfUser&id=$iduser&ordre=desc" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Plus récents d'abord</a><br>
        <a href="?action=DisplayTouiteOfUser&id=$iduser&ordre=asc" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Plus anciens d'abord</a>
        </div>
        </details> <br>
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
        $html .= <<<HTML
        <br><br>
        <a href="{$_SERVER['HTTP_REFERER']}">Retour</a>
        HTML;
        return $html;
    }



}