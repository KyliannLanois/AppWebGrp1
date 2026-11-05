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


    public function render(int $selector = self::COMPACT): string {
        switch ($selector) {
            case self::LONG:
                return $this->renderLong();
            case self::COMPACT:
            default:
                return $this->renderCompact();
        }
    }

    public abstract function renderCompact():string;
    public abstract function renderLong():string;



}