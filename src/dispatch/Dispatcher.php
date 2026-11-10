<?php

namespace touiteur\dispatch;

use touiteur\exception\RouterException;
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

    public function run(): void {
        if (!isset($this->actions[$this->action])) {
            throw new RouterException("Action '{$this->action}' not found"); //TODO routerException
        }

        $class  = $this->actions[$this->action];
        $action = new $class();
        $main   = $action->execute();   // on récupère le HTML

        $this->renderPage($main);       // c'est renderPage qui fait le echo
    }

    private function renderPage(string $main): void
    {
        if (isset($_SESSION['user'])){
            $user = $_SESSION['user'];
        } else {
            $user = 'Connexion';
        }
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
        <meta charset="UTF-8">
        <title>Touiteur</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        </head>
        <body class="dark:bg-gray-800 bg-gray-100">
        <div class="mx-auto flex min-h-screen max-w-6xl justify-center">
        

            <header class="sticky top-0 flex h-screen w-20 shrink-0 flex-col justify-between border-r border-gray-200 px-2 py-3 xl:w-72 xl:px-4">
            <h1>Touiteur</h1>
            <a href="?action=default" class="text-gray-800 dark:text-white text-red">MenuPrincipal</a>
            <div>
            
            <nav>
            <a href="?action=add-touite">Ajouter touites</a>

       HTML;
        if (isset($_SESSION['user'])){
            $html .= <<<HTML
                <a href="?action=pagePerso">{$user}</a>
                HTML;
        } else {
            $html .= <<<HTML
                <a href="?action=connexion">{$user}</a>
                HTML;
        }

        $html .= <<<HTML
           </nav>
</div>
           </header>
           <main>
           $main
           </main>
           </div>
           <footer>
           <a href="?action=connexionAdmin">Menu Admin</a>
            </footer>
           </body>
HTML;
    echo $html;
    }

}