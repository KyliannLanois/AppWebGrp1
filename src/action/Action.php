<?php

namespace touiteur\action;

/**
 * Classe de base abstraite pour une action applicative.
 *
 * Initialise les informations principales de la requête HTTP courante,
 * puis délègue le traitement concret aux classes dérivées via la méthode
 * execute(), qui doit retourner une chaîne de caractères.
 */
abstract class Action
{
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct(){

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    abstract public function execute() : string;
}