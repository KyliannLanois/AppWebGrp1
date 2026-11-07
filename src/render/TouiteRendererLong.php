<?php


namespace touiteur\render;

class TouiteRendererLong extends TouiteRendererDefault{

    public function render(int $selector = 1): string
    {
        return $this->renderLong();
    }

    public function renderLong(): string
    {


        $texte =htmlspecialchars($this->touite->getContenu());
        $auteurObj = $this->touite->getAuteur();
        $nomAuteur = $auteurObj->getUsername();
        $auteur = htmlspecialchars($nomAuteur);
        $date = $this->touite->getDate();
        $score = $this->touite->getScore();
        $touiteId = $this->touite->getIdTouite();


       return <<<HTML
    <div class="touiteDetail">
        <div class="touite-header">
          
            <div class="user-info">
                <p class="username">{$auteur}</p>
            </div>
        </div>
        <div class="touite-content">
            <p>{$texte}</p>
        </div>
        <div class="touite-footer">
            <a href="?action=dislike&touiteId={$touiteId}" class="dislike-button">Dislike</a>
            <a href="?action=like&touiteId={$touiteId}" class="like-button">Like</a>
            <span class="score">Score : {$score}</span>
            <p class="time">{$date}</p>
            <a href="?action=default">Retour</a>
            
        </div>

    </div>
    HTML;
}

    public function renderCompact(): string
    {
        return (new TouiteRendererCompact($this->touite))->renderCompact();
    }
}
