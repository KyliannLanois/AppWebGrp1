<?php


namespace touiteur\Action;

use touiteur\repository\TouiteRepository;


public class EvaluerTouiteAction extends Action{

    public function execute(){
        if ($this->http_method !== 'POST') {
            return;
        }


        $idTouite = filter_var($_POST['id_touite'] ?? null, FILTER_VALIDATE_INT);
        $valeur = filter_var($_POST['valeur'] ?? null, FILTER_VALIDATE_INT);
    }



}