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
        $this->description = $description;
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


}