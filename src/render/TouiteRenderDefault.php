<?php

namespace touiteur\render;

use touiteur\render\Renderer;

abstract class TouiteRenderDefault implements Renderer {

    // attribut touite

    public function __construct()
    {
        //Un touite
    }


    public function render(int $selector = 1): string
    {
        return match($selector) {
            self::COMPACT => $this->renderCompact(),
            self::LONG => $this->renderLong(),
            default => $this->renderCompact()
        };
    }

    public abstract function renderCompact():string;
    public abstract function renderLong():string;



}