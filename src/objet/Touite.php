<?php

namespace touiteur\objet;


/**
 * Représente un touite publié par un utilisateur.
 *
 * Un touite regroupe les informations nécessaires à son identification,
 * à son affichage et à son évaluation, notamment son auteur, sa date,
 * son contenu textuel, une image éventuelle, des tags éventuels et un score.
 */
class Touite
{
    private int $id_touite;
    private User $auteur;
    private string $date;
    private string $contenu;
    private ?Image $image;

    private ?array $tags;

    private int $score;

    public function __construct(int $id_touite, User $auteur, string $date, string $contenu, ?Image $image, ?array $tags,int $score = 0)
    {
        $this->id_touite = $id_touite;
        $this->auteur = $auteur;
        $this->date = $date;
        $this->contenu = $contenu;
        $this->image = $image;
        $this->tags = $tags;
        $this->score = $score;

    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getDate(): string
    {
        return $this->date;
    }



    public function getIdTouite(): int
    {
        return $this->id_touite;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getAuteur(): User
    {
        return $this->auteur;
    }

    public function getScore(): int
    {
        return $this->score;
    }



}