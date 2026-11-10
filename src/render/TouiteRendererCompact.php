<?php


namespace touiteur\render;

use touiteur\action\DisplayTouitesOfUserAction;

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
        <article class="touite-card touite-compact">
            <div class="touite-header">
                <a href="?action=DisplayTouiteOfUser&id={$auteurObj->getIdUser()}">{$auteur}</a>
            </div>
            <p class="touite-content">{$this->replaceTagInText($texte)}</p>
            
            <div class="touite-footer">
                
                <span class="score">Score : {$score}</span><br>
                <a href="?action=display-touite&id={$id}">Voir le détail</a>
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
