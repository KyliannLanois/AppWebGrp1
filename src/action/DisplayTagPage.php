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

        $html = "<section class='touite-action'>";
        $html .= "<h2>Touite contenant le tag : ". ucfirst($libelle_tag)."</h2>";


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