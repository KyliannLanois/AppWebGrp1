<?php

namespace touiteur\render;

/**
 * Définit un contrat pour produire une représentation textuelle selon un format sélectionné.
 *
 * Les implémentations peuvent proposer différents niveaux de détail, notamment une sortie compacte
 * ou une sortie longue, en fonction du sélecteur fourni lors du rendu.
 */
interface Renderer {
    public const COMPACT = 1;
    public const LONG = 2;

    public function render(int $selector = 1): string;
}