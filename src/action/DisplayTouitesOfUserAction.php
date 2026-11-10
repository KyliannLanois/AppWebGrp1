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
        <h2>Touites de ".$username."</h2>
         <a href="?action=abonnementUser&idFollowed={$iduser}">{$repo->estAbonne($iduser)}</a>
        <br><br>
        <details>
        <summary>Trier par</summary>
        <a href="?action=DisplayTouiteOfUser&id=$iduser&ordre=desc">Plus récents d'abord</a><br>
        <a href="?action=DisplayTouiteOfUser&id=$iduser&ordre=asc">Plus anciens d'abord</a>
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