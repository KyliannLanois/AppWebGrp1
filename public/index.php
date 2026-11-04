<?php

require_once __DIR__ . '/../vendor/autoload.php';

use touiteur\dispatch\Dispatcher;
use touiteur\repository\TouiteRepository;

try {
    TouiteRepository::setConfig(__DIR__ . "/../config/db.config.ini");

    $dispatcher = new Dispatcher();
    $dispatcher->run();


} catch (Exception $e) {
    echo "<h1>Erreur critique</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}