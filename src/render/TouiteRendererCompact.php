<?php


namespace touiteur\render;

use touiteur\action\DisplayTouitesOfUserAction;
/**
 * Fournit un rendu compact d’un touite.
 *
 * Cette classe génère une représentation HTML synthétique contenant les
 * informations essentielles du touite, notamment son auteur, son contenu,
 * son score et un lien vers l’affichage détaillé.
 *
 * Le rendu long est délégué au moteur de rendu spécialisé correspondant.
 */
class TouiteRendererCompact extends TouiteRendererDefault{



    public function renderCompact(): string{

        $id = $this->touite->getIdTouite();
        $texte =htmlspecialchars($this->touite->getContenu());
        $auteurObj = $this->touite->getAuteur();
        $nomAuteur = $auteurObj->getUsername();
        $auteur = htmlspecialchars($nomAuteur);
        $score = $this->touite->getScore();
        $touiteId = $this->touite->getIdTouite();
        return <<<HTML
        <article class="border dark:border-gray-600 dark:shadow-none dark:bg-slate-950 rounded-lg p-4">
            <div class="touite-header">
                <a href="?action=DisplayTouiteOfUser&id={$auteurObj->getIdUser()}" class="text-sm dark:text-gray-400 font-semibold text-xl mr-5 before:content-['@'] hover:underline hover:font-bold hover:text-blue-500">{$auteur}</a>
            </div>
            <div class="bg-gray-200 dark:bg-slate-900 rounded-lg p-4 m-3 dark:text-gray-400">
                {$this->replaceTagInText($texte)}
            </div>
            
            <div class="m-4 flex items-center gap-3">
                
                <span class="border rounded-full p-2 text-center text-sm font-bold dark:text-gray-400">Score : {$score}</span>
                <a href="?action=display-touite&id={$id}" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Voir le détail</a>
            </div>
        </article>
        <br>
HTML;
    } //TODO Modifier le compact ppour remttre le like et dislike, mais faut modifier LikeAction et dislike pour qu'il nous laisse sur la page courante quand on like ou dislike ( actuellement like et dilike emmene vers le display-touite

    public function renderLong(): string
    {
        return (new TouiteRendererLong($this->touite))->renderLong();
    }
}
