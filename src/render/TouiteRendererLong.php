<?php


namespace touiteur\render;

class TouiteRendererLong extends TouiteRendererDefault{

    public function renderLong($touite){

        //TODO modifier en fonction de la base de données

       return <<<HTML
    <div class="touiteDetail">
        <div class="touite-header">
          
            <div class="user-info">
                <span class="username">Username</span>
                <span class="handle">@handle</span>
            </div>
        </div>
        <div class="touite-content">
            <p>This is a sample touite content. It can contain text, images, or other media.</p>
        </div>
        <div class="touite-footer">
            <span class="score">Score : 0</span>
            <button class="dislike-button">Dislike</button>
            <button class="like-button">Like</button>
            <span class="timestamp">date</span>
            
        </div>

    </div>
    HTML;
}

    public function renderCompact(): string
    {
        return (new TouiteRendererCompact($this->touite))->renderCompact();
    }

}
