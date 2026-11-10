<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class adminPageAction extends Action
{

    public function execute(): string
    {
        $repo = TouiteRepository::getInstance();
        $influenceurs = $repo->classementInfluenceurs();

        $html = "<h3>Classement des influenceurs</h3>";
        $html .= "<table>";
        $html .= "<tr><th>Username</th><th>Nombre de touites</th><th>Nombre de followers</th></tr>";
        foreach($influenceurs as $influenceur){
            $html .= "<tr><td>".$influenceur['username']."</td><td>".$influenceur['nbTouites']."</td><td>".$influenceur['nbFollowers']."</td></tr>";
        }
        $html .= "</table>";
        $html .= "<br>";
        return $html;
    }

}