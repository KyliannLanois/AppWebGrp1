<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;
use touiteur\render\TouiteRendererCompact;

class HomeAction extends Action {

    public function execute(): string{
        $repo = TouiteRepository::getInstance();


        $ordre = (isset($_GET['ordre']) && $_GET['ordre'] === 'asc') ? 'asc' : 'desc';
        $page = isset($_GET['ordre']) ? (int)$_GET['page'] : 1;
        if ($page <1) {
            $page = 1;
        }

        $limit = 20;
        $totalTouites = $repo->countTouites();
        $totalPages = max(1,(int)ceil($totalTouites / $limit));

        if ($page>$totalPages){
            $page=$totalPages;
            }

        $touites = $repo->getTouitesPages($page, $limit, $ordre);

        $html = "<section class='touite-action'>";
        $html .= "<h2 class='text-lg dark:text-gray-400 font-bold'>Derniers touites publiés</h2>";
        $html .= <<<HTML
        <details>
        <summary>Trier par</summary>
            <a href="?action=home&ordre=desc&page=1">Plus récents d'abord</a><br>
            <a href="?action=home&ordre=asc&page=1">Plus anciens d'abord</a>
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

            $html .= "<nav class='pagination' style='margin-top: 20px; display: flex; gap: 10px; align-items: center;'>";

            if ($page > 1){
                $prev = $page - 1;
                $html .= "<a href='?action=home&ordre={$ordre}&page={$prev}'>&laquo; Précédent</a>";
            }

            $html .= "<span>Page {$page} sur {$totalPages}</span>";

            if ($page < $totalPages) {
                $next = $page + 1;
                $html .= "<a href='?action=home&ordre={$ordre}&page={$next}'>Suivant &raquo;</a>";
            }

            $html .= "</nav>";
        }

        $html .= "</section>";
        return $html;
    }


}