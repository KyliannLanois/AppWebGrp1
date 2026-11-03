<?php


namespace touiteur\render;

class TouiteRendererCompact extends TouiteRendererDefault{

    public function renderCompact($touite){

        //TODO modifier en fonction de la base de données et ecrire le code pour raccourcire le texte du touite

        return <<<HTML
    <div class="touiteCompact">
        <div class="touite-header">
          
            <div class="user-info">
                <span class="username">Username</span>
                <span class="handle">@handle</span>
            </div>
        </div>
        <div class="touite-content">
            <p>This is a sample touite content. It can contain text, images, or other media.</p>
        </div>
        
        </div>

    </div>
    HTML;
    }

    public function renderLong(): string
    {
        return (new TouiteRendererLong($this->touite))->renderLong();
    }

}
