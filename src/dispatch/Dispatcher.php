<?php

namespace touiteur\dispatch;

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
            throw new \iutnc\deefy\exception\RouterException("Action '{$this->action}' not found"); //TODO routerException
        }

        $class  = $this->actions[$this->action];
        $action = new $class();
        $html   = $action->execute();   // on récupère le HTML

        $this->renderPage($html);       // c'est renderPage qui fait le echo
    }

    private function renderPage(string $html): void
    {
        echo $html;
    }

}