<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;
use touiteur\render\TouiteRendererCompact;

/**
 * Action contrôlant l’affichage de la page d’accueil.
 *
 * Récupère les touites disponibles depuis le dépôt, applique l’ordre de tri
 * demandé, valide le numéro de page et génère le contenu HTML correspondant.
 *
 * Gère également l’affichage des liens de tri, la pagination et le message
 * présenté lorsqu’aucun touite n’est disponible.
 */
class HomeAction extends Action {

    public function execute(): string{
        $repo = TouiteRepository::getInstance();


        $ordre = (isset($_GET['ordre']) && $_GET['ordre'] === 'asc') ? 'asc' : 'desc';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $totalTouites = $repo->countTouites();
        $totalPages = max(1, (int)ceil($totalTouites / TouiteRepository::LIMITE));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $touites = $repo->getAllTouites($page, $ordre);

        $html = "<section class='touite-action'>";
        $html .= "<h2 class='text-lg dark:text-gray-400 font-bold'>Derniers touites publiés</h2>";
        $html .= <<<HTML
        <details>
        <summary class="text-lg dark:text-gray-400 font-bold">Trier par</summary>
        <div class="flex item-center gap-2">
        <a href="?action=home&ordre=desc&page=1" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Plus récents d'abord</a><br>
        <a href="?action=home&ordre=asc&page=1" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Plus anciens d'abord</a>
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