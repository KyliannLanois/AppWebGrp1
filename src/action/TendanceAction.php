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
        $html .= "<h2 class='text-2xl font-bold dark:text-white'>Les tendances (Tags les plus mentionnés)</h2>";


        if (empty($tendances)){
            $html .= "<p class='rounded-lg border border-dashed p-6 text-center text-gray-500 dark:border-gray-600 dark:text-gray-400'>Aucun tag mentionné pour le moment.</p>";
        } else {
            $html .= "<div class='overflow-x-auto rounded-lg border dark:border-gray-600 dark:bg-slate-950'>";
            $html .= "<table class='w-full text-left text-sm'>";
            $html .= "<thead class='bg-gray-100 text-xs uppercase tracking-wide text-gray-600 dark:bg-slate-900 dark:text-gray-400'><tr><th class='px-4 py-3 font-semibold'>Tag</th><th class='px-4 py-3 font-semibold'>Description</th><th class='px-4 py-3 text-right font-semibold'>Nombre de mentions</th></tr></thead>";
            $html .= "<tbody class='divide-y divide-gray-200 dark:divide-gray-700'>";

            foreach ($tendances as $tag) {
                $idTag = (int)$tag['id_tag'];
                $libelle = htmlspecialchars($tag['libelle_tag']);
                $desc = htmlspecialchars($tag['desc_tag'] ?? '-');
                $nbMentions = (int)$tag['nb_mentions'];

                $html .= "<tr class='hover:bg-gray-50 dark:hover:bg-slate-900'>";
                $html .= "<td class='px-4 py-3'><a href='?action=tagPage&id={$idTag}' class='inline-block rounded-full bg-blue-100 px-3 py-1 font-semibold text-blue-700 hover:bg-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:hover:bg-blue-900'>#{$libelle}</a></td>";
                $html .= "<td class='px-4 py-3 text-gray-700 dark:text-gray-300'>{$desc}</td>";
                $html .= "<td class='px-4 py-3 text-right font-semibold tabular-nums text-gray-900 dark:text-gray-200'>{$nbMentions}</td>";
                $html .= "</tr>";
            }

            $html .= "</tbody></table></div>";
        }
        $html .= "</section>";
        return $html;
    }

}