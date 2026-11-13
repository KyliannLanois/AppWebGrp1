<?php


namespace touiteur\action;

use touiteur\objet\Tag;
use touiteur\repository\TouiteRepository;

class AddTouitesAction extends Action {

    public function execute(): string{
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $repo = new TouiteRepository();
            $texte = trim($_POST['texte'] ?? '');

            if (empty($texte)) {
                return "<p> Le texte du touite ne peut etre vide</p>";
            }

            if (mb_strlen($texte) > 235) {
                return "<p> Le texte du touite ne peut etre plus long que 235 caractères</p>";
            }

            //Pour gerer le username
            $user = $repo->getUser(trim($_POST['username']));
            $username = $user->getUsername();
            $idTouite = $repo->addTouite($username, $texte);

            //Gestion des tags
            $Tags = Tag::getTagInText($texte);

            foreach ($Tags as $Tag) {
                // Créaation d'un tag tampon pour générer la description
                $tagObj = new Tag(1,$Tag);
                //add d'un tag avec sa description ( si le tag est déja présnet alors $id_tag sera égale a celui existant
                $id_tag = $repo->addTag($Tag,$tagObj->getDescription());
                // Link d'un tag au touite qui se crée
                $repo->linkTouiteTag($idTouite,$id_tag);
                // Link d'un tag a un utilisateur
                $repo->linkTagUser($id_tag,$user->getIdUser());
            }


            header('Location: ?action=default');
            exit();
        }
        if (isset($_SESSION['user'])){
            return $this->renderForm($_SESSION['user']);
        } else{
            return $this->renderForm('');
        }

    }


    private function renderForm(string $ancientexte): string{
        $safeText = htmlspecialchars($ancientexte);
        return <<< HTML
        <section class="add-touite-section">
            <h2 class="text-2xl font-bold dark:text-white" >Publier un nouveau touite</h2>
            <form method="POST" action="?action=add-touite">
                <div class="flex flex-col border border-gray-200 rounded-md p-4 shadow-lg dark:border-gray-400 dark:shadow-gray-600/50 dark:bg-slate-950">
                    <label for="username" class="text-sm dark:text-gray-400">Rentrer ci-dessous votre pseudo : </label><br>
                    <input type="text" id="username" name="username" maxlength="50" value="{$safeText}" class="px-2 py-1 border-3 border-gray-200 rounded-md dark:text-gray-400 dark:border-gray-600"><br>
                    <label for="texte" class="text-sm dark:text-gray-400">Votre message (235 caractères max, incluez vos #tags) :</label><br>
                    <textarea id="texte" name="texte" rows="4" cols="50" maxlength="235" required class="px-2 py-1 border border-gray-200 shadow-md dark:bg-slate-950 dark:text-gray-400 dark:border-gray-600 dark:placeholder-gray-400 dark:shadow-none"></textarea>
                </div>
                <br>
                <button type="submit" class="bg-blue-500 rounded-full p-2 text-white hover:bg-blue-600 text-center text-lg font-bold transition cursor-pointer mr-5">Touiter</button>
                <a href="?action=default" class="rounded-full p-2 font-bold text-lg text-center transition border-2 hover:bg-gray-200 border-gray-200 dark:bg-slate-950 dark:text-gray-400 dark:border-gray-600 dark:shadow-none dark:hover:bg-slate-900">Annuler</a>
            </form>
        </section>
        HTML;
    }
}
