<?php

namespace touiteur\objet;

use touiteur\repository\TouiteRepository;

/**
 * Représente un utilisateur de l’application.
 *
 * Cette classe regroupe les informations principales associées à un utilisateur,
 * notamment son identifiant, son nom d’utilisateur, ses informations personnelles
 * facultatives et le nombre de touites qui lui sont associés.
 *
 * Elle permet également d’obtenir le score de l’utilisateur à partir du dépôt
 * chargé du calcul correspondant.
 */
class User
{
    private int $id_user;
    private int $nb_touites;
    private ?string $username;
    private ?string $nom;
    private ?string $prenom;
    private ?string $email;

    public function __construct(
        int $id_user,
        ?int $nb_touites = 0,
        ?string $username = null,
        ?string $nom = null,
        ?string $prenom = null,
        ?string $email = null
    ) {
        $this->id_user = $id_user;
        $this->nb_touites = $nb_touites;
        $this->username = $username;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
    }

    public function getIdUser(): int
    {
        return $this->id_user;
    }

    public function getNbTouites(): int
    {
        return $this->nb_touites;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getScore(): ?int {
        $repository = TouiteRepository::getInstance();
        return $repository->calculerScore($this->id_user);
    }
}