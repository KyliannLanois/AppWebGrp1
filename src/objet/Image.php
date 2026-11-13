<?php

namespace touiteur\objet;

/**
 * Représente une image caractérisée par un identifiant, une description et un chemin d’accès.
 *
 * Cette classe fournit les méthodes nécessaires pour initialiser une image,
 * consulter ses informations principales et modifier ses valeurs associées.
 */
class Image
{
    private int $id;
    private string $description;
    private string $path;

    public function __construct(int $id,string $ancien_nom, string $path)
    {
        $this->id = $id;
        $this->description = "Description de l'image : ".$ancien_nom;
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }





}