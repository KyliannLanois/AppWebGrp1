<?php

namespace touiteur\dispatch;

use touiteur\exception\RouterException;
use src\action\Action;


/**
 * Répartiteur central chargé de sélectionner et d’exécuter une action applicative
 * à partir du paramètre de requête correspondant.
 *
 * Cette classe associe des noms d’actions à des classes exécutables, instancie
 * l’action demandée, récupère le contenu produit par celle-ci puis l’intègre dans
 * la page principale de l’application.
 *
 * Elle assure également la construction de l’enveloppe HTML commune, incluant la
 * navigation, les icônes SVG et les liens adaptés à l’état de session de
 * l’utilisateur.
 *
 * Une exception est levée lorsqu’aucune route enregistrée ne correspond à
 * l’action demandée.
 */
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

    private const ICONES = [
        'logo' => '<path fill="#2563eb" d="M21 6.2c-.8-.3-2.5-.3-3.6.2-1 .5-2.1 1.7-3.4 3.4l-2.8 3.6c-.2.2-.1.5.2.4l8.8-2.8c.5-.2.9-.5 1.2-1 .8-1.1 1.5-2.4 1.8-3.2.2-.4-.1-.7-.5-.7l-1.7.1zM13.5 7.5H9.2C5.9 7.5 3.5 9.8 3.5 13v1.2c0 2.1 1.1 3.9 2.7 4.9l-.9 3.1c-.1.4.3.7.7.5l3.7-2.2h5.1c3.3 0 5.7-2.3 5.7-5.5v-1.2c0-.3-.3-.5-.6-.4l-9.9 3.1c-.7.2-1.3-.5-.8-1.1l4.7-6.1c.2-.3 0-.8-.4-.8z"/>',
        'tendances' => 'M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941',
        'profil' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
        'connexion' => 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9',
        'fil' => 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z',
        'admin' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
    ];


    /**
     * Génère le balisage SVG correspondant à l’icône demandée.
     *
     * @param string $nom Nom de l’icône à générer.
     * @param string $taille Classe CSS définissant la taille de l’icône.
     * @return string Balisage SVG de l’icône.
     */
    private function icone(string $nom, string $taille = 'size-6'): string

    {

        if ($nom === 'logo') {
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="' . $taille . ' shrink-0" aria-hidden="true">'
                . self::ICONES['logo'] . '</svg>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            . 'stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="' . $taille . ' shrink-0" aria-hidden="true">'
            . '<path d="' . self::ICONES[$nom] . '"/></svg>';
    }

    /**
     * Génère et affiche la page complète de l’application avec la navigation, le contenu principal et le pied de page.
     * Recupère le rendu des actions et l'intègre pour l'affichage.
     *
     * @param string $main Contenu principal à intégrer dans la page.
     * @return void
     */
    private function renderPage(string $main): void
    {
        if (isset($_SESSION['user'])){
            $user = $_SESSION['user'];
        } else {
            $user = 'Connexion';
        }
        $iTendances = $this->icone('tendances');
        $iProfil = $this->icone('profil');
        $iFil = $this->icone('fil', 'size-5');
        $iConnexion = $this->icone('connexion', 'size-5');
        $iAdmin = $this->icone('admin', 'size-5');
        $iLogo = $this->icone('logo', 'size-8');
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
        <meta charset="UTF-8">
        <title>Touiteur</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        </head>
        
            
        <body class="dark:bg-black bg-gray-100">
        <div class="flex min-h-screen w-full">
        

            <header class="sticky top-0 flex h-screen w-64 shrink-0 flex-col justify-between border-r border-gray-200 px-3 py-4">
            <div>
            <a href="?action=default" class="mb-2 flex h-14 w-fit items-center justify-center rounded-full gap-2 px-4 text-2xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start">{$iLogo}<span class="w-fit xl:inline">Touiteur</span></a>
            <nav class="flex flex-col gap-1 dark:border-gray-600 dark:bg-slate-950 p-3">
            
            <a href="?action=afficherTendances" class="gap-2 mb-2 flex h-14 w-fit items-center justify-center rounded-full px-4 text-xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start">{$iTendances}Tendances</a>
        HTML;
        if (isset($_SESSION['user'])){
            $html .= <<<HTML
                <a href="?action=profil" class="gap-2 mb-2 flex h-14 w-fit items-center justify-center rounded-full px-4 text-xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start">{$iProfil}Profil</a>
                
                HTML;
        } else {
            $html .= <<<HTML
                <a href="?action=connexion" class="gap-2 mb-2 flex h-14 w-fit items-center justify-center rounded-full px-4 text-xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start">{$iProfil}Profil</a>
                HTML;
        }
        if (isset($_SESSION['user'])){
            $html .= <<<HTML
                <a href="?action=pagePerso" class="gap-2 mb-2 flex h-14 w-fit items-center justify-center rounded-full px-4 text-xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start">{$iFil}Mon fil</a>
                HTML;
        } else {
            $html .= <<<HTML
                <a href="?action=connexion" class="gap-2 mb-2 flex h-14 w-fit items-center justify-center rounded-full px-4 text-xl font-extrabold dark:text-white dark:hover:bg-gray-900 text-black hover:bg-gray-200 xl:justify-start">{$iConnexion}Connexion</a>
                HTML;
        }
        $html .= <<<HTML
            <a href="?action=add-touite" class="mt-4 flex items-center justify-center rounded-full bg-blue-500 px-4 py-3 text-base font-bold text-white hover:bg-blue-600 transition whitespace-nowrap">Ajouter touites</a>
       HTML;

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
           <a href="?action=connexionAdmin" class="flex w-fit items-center gap-2 rounded-full px-3 py-2 text-sm text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900">{$iAdmin}Menu Admin</a>
            </footer>
           </body>
HTML;
    echo $html;
    }

}