<?php

namespace touiteur\objet;

class User
{

    private int $id_user;
    private string $username;
    private string $nom;
    private string $prenom;
    private string $email;
    private int $nb_touites;
    public function __construct(int $id_user,string $username, string $nom, string $prenom, string $email, int $nb_touites)
    {
        $this->id_user = $id_user;
        $this->username = $username;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->nb_touites = $nb_touites;
    }

    public function getIdUser(): int
    {
        return $this->id_user;
    }

    public function getNbTouites(): int
    {
        return $this->nb_touites;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getUsername(): string{
        return $this->username;
    }



}