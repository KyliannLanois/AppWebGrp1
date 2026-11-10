<?php

namespace touiteur\objet;


class Touite
{
    private int $id_touite;
    private User $auteur;
    private string $date;
    private string $contenu;
    private ?string $image;

    private ?array $tags;

    private int $score;

    public function __construct(int $id_touite, User $auteur, string $date, string $contenu, ?string $image, ?array $tags,int $score = 0)
    {
        $this->id_touite = $id_touite;
        $this->auteur = $auteur;
        $this->date = $date;
        $this->contenu = $contenu;
        $this->image = $image;
        $this->tags = $tags;
        $this->score = $score;

    }

    public function getImage(): string
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

    public function getIdUser(): int
    {
        return $this->id_user;
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