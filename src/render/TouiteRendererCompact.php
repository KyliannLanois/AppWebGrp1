<?php


namespace touiteur\render;

class TouiteRendererCompact extends TouiteRendererDefault{

    public function render(int $selector = 1):string{
        return $this->renderCompact();
    }

    public function renderCompact(): string{

        $id = $this->touite->getIdTouite();
        $texte =htmlspecialchars($this->touite->getContenu());
        $auteurObj = $this->touite->getAuteur();
        $nomAuteur = $auteurObj->getNom();
        $auteur = htmlspecialchars($nomAuteur);

        //$score=$this->touite->getScore(); //TODO faire le score

        return <<<HTML
        <article class="touite-card touite-compact">
            <div class="touite-header">
                <strong>{$auteur}</strong>
            </div>
            <p class="touite-content">{$texte}</p>
            <div class="touite-footer">
                
                <a href="?action=display-touite&id={$id}">Voir le détail</a>
            </div>
        </article>
HTML;
    } // TODO a rajouter ça <span>Score : {$score}</span> |

    public function renderLong(): string
    {
        return $this->renderCompact();
    }

}
