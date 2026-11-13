<?php

namespace touiteur\objet;

/**
 * Représente un tag identifié par un libellé et une description associée.
 *
 * Fournit l’accès aux informations principales du tag et permet d’extraire
 * les libellés de tags présents dans un texte à partir du préfixe dièse.
 */
class Tag
{
    private int $id_tag;
    private string $libelle;
    private string $description;

    public function __construct(int $id_tag, string $libelle)
    {
        $this->id_tag = $id_tag;
        $this->libelle = $libelle;
        $this->description = "Voilà ce qui concerne : {$libelle}";
    }

    public function getIdTag(): int
    {
        return $this->id_tag;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public static function getTagInText(string $text): array {
        $pattern = "/#(\w+)/u";
        if (preg_match_all($pattern, $text, $matches)) {
            return array_values(array_unique($matches[1]));
        }
        return [];
    }

}