<?php

namespace touiteur\render;

use touiteur\objet\Tag;
use touiteur\objet\Touite;
use touiteur\render\Renderer;
use touiteur\repository\TouiteRepository;

/**
 * Classe abstraite de base pour le rendu d’un touite.
 *
 * Fournit le comportement commun aux rendus de touites, notamment la sélection
 * du format d’affichage et la transformation des tags présents dans le texte.
 * Les classes dérivées doivent définir les rendus compact et détaillé selon
 * leurs besoins.
 *
 * Le rendu s’appuie sur un touite à afficher et sur un dépôt permettant de
 * récupérer les informations associées aux tags détectés dans son contenu.
 *
 * @implements Renderer
 */
abstract class TouiteRendererDefault implements Renderer {

    protected Touite $touite;
    protected TouiteRepository $repo;

    /**
     * Initialise une nouvelle instance avec le touite et le dépôt associés.
     *
     * @param Touite $touite Le touite à utiliser.
     * @param TouiteRepository $repo Le dépôt permettant d’accéder aux touites.
     * @return void
     */
    public function __construct(Touite $touite, TouiteRepository $repo)
    {
        $this->touite = $touite;
        $this->repo = $repo;
    }


    /**
     * Génère la représentation textuelle selon le format demandé.
     *
     * @param int $selector Sélecteur de rendu à utiliser, par défaut le format compact.
     * @return string Représentation générée dans le format sélectionné.
     */
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

    /**
     * Génère la représentation textuelle longue de l'objet.
     *
     * @return string Représentation générée au format long.
     */
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
                $patternReplace = '<a href="?action=tagPage&id=' . $id . '" class="text-blue-500 hover:underline">#' . htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') . '</a>';
                // remplacement du tag en cours dans le texte grace au patern prédéfinis
                $text = preg_replace($pattern, $patternReplace, $text);
            }
        }
        return $text;
    }





}