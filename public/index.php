<?php

require_once __DIR__ . '/../vendor/autoload.php';

use touiteur\repository\TouiteRepository;
use touiteur\dispatch\Dispatcher;
use touiteur\action\HomeAction;
use touiteur\action\AddTouitesAction;

try {
    TouiteRepository::setConfig(__DIR__ . "/../config/db.config.ini");

    $dispatcher = new Dispatcher();

    $dispatcher->addRoute('default', HomeAction::class);
    $dispatcher->addRoute('home', HomeAction::class);
    $dispatcher->addRoute('add-touite', AddTouitesAction::class);
    $dispatcher->run();


} catch (Exception $e) {
    echo "<h1>Erreur critique</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}