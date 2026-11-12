<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class AfficherAbonneeAction extends Action
{

    public function execute(): string
    {
        if (!isset($_SESSION['user'])){
            return "<p>Vous devez être connecté pour voir vos abonnés.</p>";
        }


        $repo = TouiteRepository::getInstance();
        $user = $repo->getUser($_SESSION['user']);
        $user_id = $user->getIdUser();

        $abonnees = $repo->getAllAbonnees($user_id);
        $scoreMoyen = $repo->getScoreMoyenUser($user_id);

        $html = "<section class='narcissique-action'>";
        $html .= "<h3>Vos statistiques et abonnés</h3>";
        $html .= "<p><strong>Score moyen de vos touites :</strong> {$scoreMoyen}</p>";


        if (empty($abonnees)){
            $html .= "<p>Vous n'avez aucun abonné pour l'instant</p>";
        } else {
            $html .= "<p><strong>Nombre d'abonnés :</strong> " . count($abonnees) . "</p>";
            $html .= "<table>";
            $html .= "<thead><tr><th>Username</th><th>Prénom</th><th>Nom</th><th>Touites publiés</th></tr></thead>";
            $html .= "<tbody>";

            foreach ($abonnees as $abonnee) {
                $username = htmlspecialchars($abonnee['username']);
                $prenom = htmlspecialchars($abonnee['prenom_user'] ?? '-');
                $nom = htmlspecialchars($abonnee['nom_user'] ?? '-');
                $nbTouites = (int)$abonnee['nb_touites'];

                $html .= "<tr>";
                $html .= "<td>@{$username}</td>";
                $html .= "<td>{$prenom}</td>";
                $html .= "<td>{$nom}</td>";
                $html .= "<td>{$nbTouites}</td>";
                $html .= "</tr>";
            }

            $html .= "</tbody></table>";
        }
        $html .= "</section>";
        return $html;
    }

}