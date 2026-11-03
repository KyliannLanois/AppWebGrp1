<?php

require_once __DIR__ . '/../vendor/autoload.php';

use touiteur\repository\TouiteRepository;

try {
    TouiteRepository::setConfig(__DIR__ . "/../config/db.config.ini");

    $repo = TouiteRepository::getInstance();

    $touites = $repo->getAllTouites();

    echo "<h1>Connexion BDD & TouiteRepository : OK !</h1>";
    echo "<pre>";
    print_r($touites);
    echo "</pre>";

    } catch (Exception $e) {
        echo "<h1>Erreur</h1>";
        echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    }