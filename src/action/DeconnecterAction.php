<?php

namespace touiteur\action;

class DeconnecterAction extends Action
{
    public function execute(): string
    {
        $_SESSION['user'] = null;
        header('Location: ?action=connexion');
        exit();
    }
}