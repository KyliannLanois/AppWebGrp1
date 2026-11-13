<?php

namespace touiteur\action;

use touiteur\action\Action;
use touiteur\render\TouiteRendererCompact;
use touiteur\repository\TouiteRepository;

class DisplayTagPage extends Action
{

    public function execute(): string
    {

        $repo = TouiteRepository::getInstance();
        $id_tag= trim($_GET['id']);
        $tagObj = $repo->getTagById($id_tag);

        $libelle_tag = $tagObj->getLibelle();

        $touites = $repo->getListTouiteTag($id_tag);

        $tagTitre = htmlspecialchars(ucfirst($libelle_tag));
        $html = <<<HTML
            <section class='flex flex-col gap-2 '>
            <h1 class="text-lg dark:text-gray-400 font-bold">Touite contenant le tag : {$tagTitre}</h1>
            <a href="?action=abonnementTag&userId={$tagObj->getIdTag()}&tagId={$id_tag}" class="border rounded p-2 hover:bg-gray-300 dark:text-gray-400 dark:hover:bg-gray-800 w-16 font-bold">{$repo->suisTag($id_tag)}</a>
            <br><br>
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