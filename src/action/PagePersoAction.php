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

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $limit = 20;
        $totalTouites = $repo->countTouitesAbonnement($userId);
        $totalPages = max(1, (int)ceil($totalTouites / $limit));

        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $touites = $repo->getTouiteAbonnemenPage($userId,$page,$limit);

        $html = <<<HTML
        <div>
            <h3>Votre page personnalisée</h3>
            <a href="?action=profil">Accedez à votre profil</a><br><br
        </div>
HTML;

        if (empty($touites)) {
            $html .= "<p> Aucun touite pour le moment</p>";
        } else {
            foreach($touites as $touite){
                $renderer = new TouiteRendererCompact($touite,$repo);
                $html .= $renderer->render();
            }

            $html .= "<nav class='pagination' style='margin-top: 20px; display: flex; gap: 10px; align-items: center;'>";

            if ($page > 1) {
                $prev = $page - 1;
                $html .= "<a href='?action=pagePerso&page={$prev}'>&laquo; Précédent</a>";
            }

            $html .= "<span>Page {$page} sur {$totalPages}</span>";

            if ($page < $totalPages) {
                $next = $page + 1;
                $html .= "<a href='?action=pagePerso&page={$next}'>Suivant &raquo;</a>";
            }

            $html .= "</nav>";
        }

        $html .= "</section>";
        return $html;

    }
}