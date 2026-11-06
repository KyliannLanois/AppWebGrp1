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
            <button class="dislike-button">Dislike</button>
            <button class="like-button">Like</button>
            <span class="score">Score : 0</span>
            <p class="time">{$date}</p>
            
        </div>

    </div>
    HTML;
}

    public function renderCompact(): string
    {
        return (new TouiteRendererCompact($this->touite))->renderCompact();
    }
}
