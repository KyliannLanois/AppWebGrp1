<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

class TendanceAction extends Action
{

    public function execute(): string
    {

        $repo = TouiteRepository::getInstance();
        $tendances = $repo->getTendances();

        $html = "<section class='tendances-action'>";
        $html .= "<h2>Les tendances (Tags les plus mentionnés)</h2>";


        if (empty($tendances)){
            $html .= "<p>Aucun tag mentionné pour le moment.</p>";
        } else {
            $html .= "<table>";
            $html .= "<thead><tr><th>Tag</th><th>Description</th><th>Nombre de mentions</th></tr></thead>";
            $html .= "<tbody>";

            foreach ($tendances as $tag) {
                $idTag = (int)$tag['id_tag'];
                $libelle = htmlspecialchars($tag['libelle_tag']);
                $desc = htmlspecialchars($tag['desc_tag'] ?? '-');
                $nbMentions = (int)$tag['nb_mentions'];

                $html .= "<tr>";
                $html .= "<td><a href='?action=tagPage&id={$idTag}'>#{$libelle}</a></td>";
                $html .= "<td>{$desc}</td>";
                $html .= "<td>{$nbMentions}</td>";
                $html .= "</tr>";
            }

            $html .= "</tbody></table>";
        }
        $html .= "</section>";
        return $html;
    }

}