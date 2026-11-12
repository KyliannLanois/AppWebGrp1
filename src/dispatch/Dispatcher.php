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
        
            
        <body class="dark:bg-black bg-gray-100">
        <div class="flex min-h-screen mx-auto flex min-h-screen max-w-6xl justify-center">
        

            <header class="sticky top-0 flex h-screen w-48 shrink-0 flex-col justify-between border-r border-gray-200 px-3 py-4">
            <div>
            <a href="?action=default" class="mb-2 flex h-14 w-fit items-center justify-center rounded-full px-4 text-2xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start"><span class="w-fit xl:inline">Touiteur</span></a>
            <nav class="flex flex-col gap-1 border-t dark:border-gray-600 dark:bg-slate-950">
            
            <a href="?action=afficherTendances" class="mb-2 flex h-14 w-fit items-center justify-center rounded-full px-4 text-xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start">Tendances</a>
            <a href="?action=add-touite" class="mt-4 flex items-center justify-center rounded-full bg-blue-500 py-3 text-lg font-bold text-white hover:bg-blue-600 transition">Ajouter touites</a>

       HTML;
        if (isset($_SESSION['user'])){
            $html .= <<<HTML
                <a href="?action=pagePerso" class="rounded-full px-3 py-2 text-center text-sm text-gray-500 hover:bg-gray-200 xl:text-left">{$user}</a>
                HTML;
        } else {
            $html .= <<<HTML
                <a href="?action=connexion" class="rounded-full px-3 py-2 text-center text-sm text-gray-500 hover:bg-gray-200 xl:text-left dark:hover:bg-gray-900">{$user}</a>
                HTML;
        }

        $html .= <<<HTML
           </nav>
</div>
           </header>
           <main class="flex-1">
           <div class="mx-auto max-w-xl p-4">
           
           $main
</div>
           </main>
           </div>
           <footer>
           <a href="?action=connexionAdmin" class="rounded-full px-3 py-2 text-center text-sm text-gray-500 hover:bg-gray-200 xl:text-left">Menu Admin</a>
            </footer>
           </body>
HTML;
    echo $html;
    }

}