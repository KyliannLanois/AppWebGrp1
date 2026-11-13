<?php

namespace touiteur\action;

/**
 * Action chargée de déconnecter l'utilisateur courant.
 *
 * Réinitialise les informations d'utilisateur conservées en session, redirige
 * vers l'action de connexion, puis interrompt l'exécution du script.
 */
class DeconnecterAction extends Action
{
    public function execute(): string
    {
        $_SESSION['user'] = null;
        header('Location: ?action=connexion');
        exit();
    }
}