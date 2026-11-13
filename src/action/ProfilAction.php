<?php

namespace touiteur\action;

use touiteur\render\TouiteRendererCompact;
use touiteur\repository\TouiteRepository;

/**
 * Action chargée d’afficher le profil de l’utilisateur connecté.
 *
 * Récupère l’utilisateur courant depuis la session, charge ses informations
 * et ses touites depuis le dépôt, puis construit le contenu HTML de la page
 * de profil. La page affiche un message de bienvenue, un lien vers la liste
 * des abonnés, les touites publiés par l’utilisateur, son score total ainsi
 * qu’un lien de déconnexion.
 *
 * @return string Contenu HTML représentant la page de profil de l’utilisateur connecté.
 */
class ProfilAction extends Action
{
    public function execute(): string
    {

        $repo = TouiteRepository::getInstance();
        $userName = $_SESSION["user"];
        $user = $repo->getUser($userName);
        $userId = $user->getIdUser();

        $touites = $repo->getListTouiteUser($userId);

        $html = "<h2 class='text-xl dark:text-gray-400 font-bold'>Bienvenue ".$userName."</h2>";
        $html .= "<section class='flex flex-col gap-2'>";
        $html .= "<a href='?action=afficherAbonnee' class='text-lg text-blue-500 hover:text-blue-600 border rounded p-2 hover:bg-blue-100 font-bold dark:hover:bg-gray-800'>Afficher la liste de ses abonnées</a>";
        $html .= "<h3 class='text-lg dark:text-gray-400 font-bold'>Voici vos touites :</h3>";



        if (empty($touites)) {
            $html .= "<p> Aucun touite pour le moment</p>";
        } else {
            foreach($touites as $touite){
                $renderer = new TouiteRendererCompact($touite,$repo);
                $html .= $renderer->render();
            }
        }

        $html .= "</section>";

        $html .= <<<HTML
<section class="scoreProfil">
<p class="text-md dark:text-gray-400">Votre score total est de : {$user->getScore()}</p>
</section>
<section class="flex flex-col gap-2 w-34">
<a href="?action=deconnexion" class="border rounded p-2 hover:bg-gray-300 dark:text-gray-400 dark:hover:bg-gray-300">Se déconnecter</a>
</section>
HTML;

        return $html;
    }
}