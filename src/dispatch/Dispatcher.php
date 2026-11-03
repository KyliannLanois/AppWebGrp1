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
        echo $html;
    }

}