<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

/**
 * Action chargée d'afficher les statistiques d'abonnement de l'utilisateur connecté.
 *
 * Cette action vérifie la présence d'une session utilisateur, récupère les informations
 * de l'utilisateur courant, son score moyen de touites ainsi que la liste de ses abonnés.
 * Elle génère ensuite le contenu destiné à présenter le nombre d'abonnés, leurs informations
 * principales et le nombre de touites publiés par chacun.
 *
 * Si aucun utilisateur n'est connecté, un message d'information est retourné afin
 * d'indiquer que l'accès à ces données nécessite une authentification.
 */
class AfficherAbonneeAction extends Action
{

    public function execute(): string
    {
        if (!isset($_SESSION['user'])){
            return "<p class='text-lg dark:text-gray-400'>Vous devez être connecté pour voir vos abonnés.</p>";
        }


        $repo = TouiteRepository::getInstance();
        $user = $repo->getUser($_SESSION['user']);
        $user_id = $user->getIdUser();

        $abonnees = $repo->getAllAbonnees($user_id);
        $scoreMoyen = $repo->getScoreMoyenUser($user_id);

        $html = "<section class='narcissique-action'>";
        $html .= "<h3 class='text-lg dark:text-gray-400 font-bold'>Vos statistiques et abonnés</h3>";
        $html .= "<p class='text-lg dark:text-gray-400'><strong>Score moyen de vos touites :</strong> {$scoreMoyen}</p>";


        if (empty($abonnees)){
            $html .= "<p class='dark:text-gray-400'>Vous n'avez aucun abonné pour l'instant</p>";
        } else {
            $html .= "<p class='mb-3 text-sm text-gray-600 dark:text-gray-400'><strong class='font-semibold text-gray-900 dark:text-gray-200'>Nombre d'abonnés :</strong> " . count($abonnees) . "</p>";
            $html .= "<div class='overflow-x-auto rounded-lg border dark:border-gray-600 dark:bg-slate-950'>";
            $html .= "<table class='w-full text-left text-sm'>";
            $html .= "<thead class='bg-gray-100 text-xs uppercase tracking-wide text-gray-600 dark:bg-slate-900 dark:text-gray-400'>";
            $html .= "<tr><th class='px-4 py-3 font-semibold'>Username</th><th class='px-4 py-3 font-semibold'>Prénom</th><th class='px-4 py-3 font-semibold'>Nom</th><th class='px-4 py-3 text-right font-semibold'>Touites publiés</th></tr>";
            $html .= "</thead>";
            $html .= "<tbody class='divide-y divide-gray-200 dark:divide-gray-700'>";

            foreach ($abonnees as $abonnee) {
                $username = htmlspecialchars($abonnee['username']);
                $prenom = htmlspecialchars($abonnee['prenom_user'] ?? '-');
                $nom = htmlspecialchars($abonnee['nom_user'] ?? '-');
                $nbTouites = (int)$abonnee['nb_touites'];

                $html .= "<tr class='hover:bg-gray-50 dark:hover:bg-slate-900'>";
                $html .= "<td class='px-4 py-3 font-semibold text-blue-600 dark:text-blue-400'>@{$username}</td>";
                $html .= "<td class='px-4 py-3 text-gray-800 dark:text-gray-300'>{$prenom}</td>";
                $html .= "<td class='px-4 py-3 text-gray-800 dark:text-gray-300'>{$nom}</td>";
                $html .= "<td class='px-4 py-3 text-right tabular-nums text-gray-800 dark:text-gray-300'>{$nbTouites}</td>";
                $html .= "</tr>";
            }

            $html .= "</tbody></table>";
        }
        $html .= "</section>";
        return $html;
    }

}