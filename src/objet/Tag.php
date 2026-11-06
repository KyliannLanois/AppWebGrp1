<?php

namespace touiteur\objet;

class Tag
{
    private int $id_tag;
    private string $libelle;
    private string $description;

    public function __construct(int $id_tag, string $libelle, string $description)
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

    public function getTagInText(string $text): array{
        $arrayTag = [];

        $pattern ="/#(\w+)/u";

        if(preg_match_all($pattern,$text,$matches)){
            $libelles = $matches[0];
        }

        array_unique($arrayTag);


        return $arrayTag;
    }

}