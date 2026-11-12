<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;
use touiteur\render\TouiteRendererCompact;

class HomeAction extends Action {

    public function execute(): string{
        $repo = TouiteRepository::getInstance();
        if (isset($_GET['ordre']) && $_GET['ordre'] == 'asc') {
            $touites = $repo->getAllTouitesInverse();
        } else {
            $touites = $repo->getAllTouites();
        }

        $html = "<section class='touite-action'>";
        $html .= "<h2 class='text-lg dark:text-gray-400 font-bold'>Derniers touites publiés</h2>";
        $html .= <<<HTML
        <details>
        <summary class="text-lg dark:text-gray-400 font-bold">Trier par</summary>
        <div class="flex item-center gap-2">
        <a href="?action=home&ordre=desc" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Plus récents d'abord</a><br>
        <a href="?action=home&ordre=asc" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Plus anciens d'abord</a>
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
        return $html;
    }


}