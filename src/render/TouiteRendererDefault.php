<?php

namespace touiteur\render;

use touiteur\objet\Tag;
use touiteur\objet\Touite;
use touiteur\render\Renderer;
use touiteur\repository\TouiteRepository;

abstract class TouiteRendererDefault implements Renderer {

    protected Touite $touite;
    protected TouiteRepository $repo;

    public function __construct(Touite $touite,TouiteRepository $repo)
    {
        $this->touite = $touite;
        $this->repo = $repo;
    }


    public function render(int $selector = self::COMPACT): string {
        switch ($selector) {
            case self::LONG:
                return $this->renderLong();
            case self::COMPACT:
            default:
                return $this->renderCompact();
        }
    }

    public abstract function renderCompact():string;
    public abstract function renderLong():string;

    /**
     * Méthode qui permet la modification du texte afin de pouvoir afficher les Tag en tant que lien
     * @param string $text
     * @return string Le texte modifier pour afficher les Tag comme liens
     */
    public function replaceTagInText(string $text): string
    {
        // on récupere les Tag présent dans le texte
        $tags = Tag::getTagInText($text);
        // On parcours les tags un par un
        foreach ($tags as $libelle) {
            // On crée l'objet TAG
            $tag = $this->repo->getTag($libelle);

            if($tag !== null) {
                //On récupère l'id du tag afin de pouvoir l'inserer dans dans la lien action
                $id = $tag->getIdTag();
                //Pattern de tri pour trouver le # + libelle afin d'associer le bon lien au bon tag
                $pattern = '/#' . preg_quote($libelle, '/') . '(?![\p{L}\p{N}_])/u';
                // Pattern de remplacement, création de l'URL action avec son nom ainsi que l'id du tag
                $patternReplace = '<a href="?action=tagPage&id=' . $id . '">#' . htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') . '</a>';
                // remplacement du tag en cours dans le texte grace au patern prédéfinis
                $text = preg_replace($pattern, $patternReplace, $text);
            }
        }
        return $text;
    }





}