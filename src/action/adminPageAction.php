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
        $html .= "<table>";
        $html .= "<tr><th>Username</th><th>Nombre de touites</th><th>Nombre de followers</th></tr>";
        foreach($influenceurs as $influenceur){
            $html .= "<tr><td>".htmlspecialchars($influenceur['username'])."</td><td>".(int)$influenceur['nbTouites']."</td><td>".(int)$influenceur['nbFollowers']."</td></tr>";
        }
        $html .= "</table>";
        $html .= "<br>";
        return $html;
    }

}