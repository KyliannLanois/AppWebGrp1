<?php

namespace src\dispatch;

use src\action\Action;



class Dispatcher
{
    protected ?string $action = null;
    protected $actions = [];

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $action_class): Dispatcher
    {
        $this->actions[$action] = $action_class;
        return $this;
    }

    public function run(): void
    {
        if (isset($this->actions[$this->action])) {
            $classe = new $this->actions[$this->action]();
            $this->renderPage($classe->execute());
        } else {
            $classe = new DefaultAction();
            $this->renderPage($classe->execute());
        }
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
            <a href="?action=menuPrincipal">MenuPrincipal</a>
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