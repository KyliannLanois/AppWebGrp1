<?php

namespace touiteur\action;

use touiteur\render\TouiteRendererCompact;
use touiteur\repository\TouiteRepository;

/**
 * Action chargée de générer la page personnalisée de l’utilisateur connecté.
 *
 * Récupère les touites provenant des abonnements de l’utilisateur, applique une
 * pagination sécurisée à partir du paramètre de requête, puis produit le contenu
 * HTML correspondant avec les liens de navigation entre les pages disponibles.
 *
 * Cette action s’appuie sur l’utilisateur enregistré en session et sur le dépôt
 * des touites pour déterminer les contenus à afficher.
 */
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

        $totalTouites = $repo->countTouitesAbonnement($userId);
        $totalPages = max(1, (int)ceil($totalTouites / TouiteRepository::LIMITE));

        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $touites = $repo->getTouiteAbonnement($userId, $page);

        $html = <<<HTML
        <div class="flex flex-col gap-2">
            <h1 class="text-lg dark:text-gray-400 font-bold ">Votre page personnalisée</h1>
            <a href="?action=profil" class="border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700 text-center font-bold dark:text-gray-400">Accedez à votre profil</a><br><br>
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