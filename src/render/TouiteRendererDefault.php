<?php

namespace touiteur\render;

use touiteur\objet\Touite;
use touiteur\render\Renderer;

abstract class TouiteRendererDefault implements Renderer {

    protected Touite $touite;

    public function __construct(Touite $touite)
    {
        $this->touite = $touite;
    }


    public abstract function render(int $selector = 1) : string;

    public abstract function renderCompact():string;
    public abstract function renderLong():string;



}