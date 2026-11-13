<?php


namespace touiteur\render;

use touiteur\repository\TouiteRepository;

class TouiteRendererLong extends TouiteRendererDefault{

    public function render(int $selector = 1): string
    {
        return $this->renderLong();
    }

    public function renderLong(): string
    {

        $pouce = 'M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75A2.25 2.25 0 0 1 16.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z';
        $texte =htmlspecialchars($this->touite->getContenu());
        $auteurObj = $this->touite->getAuteur();
        $nomAuteur = $auteurObj->getUsername();
        $auteur = htmlspecialchars($nomAuteur);
        $date = $this->touite->getDate();
        $score = $this->touite->getScore();
        $touiteId = $this->touite->getIdTouite();
        $iduser = $auteurObj->getIdUser();
        $imgPath = $this->touite->getImage()->getPath();


       return <<<HTML
    <div class="border dark:border-gray-600 dark:shadow-none dark:bg-slate-950 rounded-lg p-4 flex flex-col gap-4">
        <div class="touite-header">
          
            <div class="user-info">
                <a href="?action=DisplayTouiteOfUser&id={$iduser}" class="text-sm dark:text-gray-400 font-semibold text-xl mr-5 before:content-['@'] hover:underline hover:font-bold hover:text-blue-500">{$auteur}</a>
                <a href="?action=abonnementUser&idFollowed={$iduser}&touiteId={$touiteId}" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">{$this->repo->estAbonne($iduser)}</a>
            </div>
        </div>
        <div class="bg-gray-200 dark:bg-slate-900 rounded-lg p-4 m-3 dark:text-gray-400">
            <p>{$this->replaceTagInText($texte)}</p>
            <img src=$imgPath alt="Image du touite">
        </div>
        <div class="m-4 flex items-center gap-3">
            <a href="?action=dislike&touiteId={$touiteId}" class="inline-flex rounded-full p-2 text-gray-500 hover:bg-red-100 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-6 -scale-y-100">
        <path d="{$pouce}"/></svg></a>
            <a href="?action=like&touiteId={$touiteId}" class="inline-flex rounded-full p-2 text-gray-500 hover:bg-green-100 hover:text-green-600 transition"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-6">
        <path d="{$pouce}"/>
    </svg></a>
            <span class="border rounded-full p-2 text-center text-sm font-bold dark:text-gray-400">Score : {$score}</span>
            <p class="text-sm dark:text-gray-400">{$date}</p>
            <a href="{$_SERVER['HTTP_REFERER']}" class="text-sm dark:text-gray-400 font-semibold border rounded p-2 hover:bg-gray-300 dark:hover:bg-gray-700">Retour</a>
            
        </div>

    </div>
    HTML;
       }


    public function renderCompact(): string
    {
        return (new TouiteRendererCompact($this->touite))->renderCompact();
    }
}
