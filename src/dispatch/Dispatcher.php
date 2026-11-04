<?php

namespace touiteur\dispatch;

use touiteur\exception\RouterException;
use touiteur\action\Action;



class Dispatcher
{
    protected ?string $action = null;
    protected array $actions = [];

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $action_class): Dispatcher
    {
        $this->actions[$action] = $action_class;
        return $this;
    }

    public function run(): void {
        if (!isset($this->actions[$this->action])) {
            throw new RouterException("Action '{$this->action}' not found"); //TODO routerException
        }

        $class  = $this->actions[$this->action];
        $action = new $class();
        $html   = $action->execute();   // on récupère le HTML

        $this->renderPage($html);       // c'est renderPage qui fait le echo
    }

    private function renderPage(string $html): void
    {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
        <meta charset="UTF-8">
        <title>Touiteur</title>
        </head>
        <body>
            <header>
            <h1>Touiteur</h1>
            <nav>
            <a href="?action=default">MenuPrincipal</a>
            <a href="?action=add-touite">Ajouter touites</a>
            <a href="?action=menuAdmin">Menu Admin</a>
            <a href="?action=connexion">Connexion</a>
           </nav>
           </header>
           <main>
           $html;
           </main>
           </body>
HTML;

    }

}