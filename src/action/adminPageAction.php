<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

/**
 * Action d'administration chargée de générer l'affichage du classement des influenceurs.
 *
 * Récupère les données de classement depuis le dépôt des touites et produit une chaîne
 * de caractères représentant le contenu à afficher, incluant le nom d'utilisateur,
 * le nombre de touites publiés et le nombre d'abonnés pour chaque influenceur.
 */
class adminPageAction extends Action
{

    public function execute(): string
    {
        if (empty($_SESSION['admin'])) {
            header('Location: ?action=connexionAdmin');
            exit();
        }
        $repo = TouiteRepository::getInstance();
        $influenceurs = $repo->classementInfluenceurs();

        $html = "<h1 class='text-lg dark:text-gray-400 font-bold'>Classement des influenceurs</h1>";
        $html .= "<div class='overflow-x-auto rounded-lg border dark:border-gray-600 dark:bg-slate-950'><table class='w-full text-left text-sm'>";
        $html .= "<thead class='bg-gray-100 text-xs uppercase tracking-wide text-gray-600 dark:bg-slate-900 dark:text-gray-400'><tr><th class='px-4 py-3 font-semibold'>Username</th><th class='px-4 py-3 font-semibold'>Nombre de touites</th><th class='px-4 py-3 font-semibold text-right'>Nombre de followers</th></tr></thead>";
        $html .= "<tbody class='divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300'>";
        foreach($influenceurs as $influenceur){
            $html .= "<tr><td>".htmlspecialchars($influenceur['username'])."</td><td>".(int)$influenceur['nbTouites']."</td><td>".(int)$influenceur['nbFollowers']."</td></tr>";
            $html .= "<tr class='hover:bg-gray-50 dark:hover:bg-slate-900'><td class='px-4 py-3'>".$influenceur['username']."</td><td class='px-4 py-3'>".$influenceur['nbTouites']."</td><td class='px-4 py-3 text-right'>".$influenceur['nbFollowers']."</td></tr>";
        }
        $html .= "</table></div>";
        $html .= "<br>";
        return $html;
    }

}