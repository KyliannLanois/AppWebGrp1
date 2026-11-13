<?php


namespace touiteur\action;

use finfo;
use Random\RandomException;
use touiteur\objet\Image;
use touiteur\objet\Tag;
use touiteur\objet\Touite;
use touiteur\repository\TouiteRepository;

class AddTouitesAction extends Action {


    public function execute(): string{
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $repo = new TouiteRepository();
            $id_img = null;
            $texte = trim($_POST['texte'] ?? '');


            if (empty($texte)) {
                return "<p class='text-sm dark:text-gray-400'> Le texte du touite ne peut etre vide</p>";
            }

            if (mb_strlen($texte) > 235) {
                return "<p class='text-sm dark:text-gray-400'> Le texte du touite ne peut etre plus long que 235 caractères</p>";
            }




            //gestion image

            if(isset($_FILES['img']) && $_FILES['img']['error'] !== UPLOAD_ERR_NO_FILE){
                $file = $_FILES['img'];
                if($file['error'] !== UPLOAD_ERR_OK){
                    return "<p class='text-sm dark:text-gray-400'> Erreur dans l'envoi de l'image</p>";
                }
                $maxSize = 20 * 1024 * 1024;


                if($file['size'] > $maxSize) {
                    return "<p class='text-sm dark:text-gray-400'>L'image est trop lourde</p>";
                }

//                $finfo = new finfo(FILEINFO_MIME_TYPE);
//                $formatOk = ["jpg", "jpeg", "png", "webp"];
//                $type = $finfo->file($file['tmp_name']);
//                if(! in_array($type, $formatOk)) {
//                    return "<p>Format non admis ! </p>";
//                }

                //TODO add test format

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid() . "." . $extension;

                if(!move_uploaded_file($file['tmp_name'], "uploads/" . $newFileName)){
                    return "<p>Erreur lors de l'envoie de la photo</p>";
                }
                $id_img= $repo->addImg($file["tmp_name"],"uploads/" .$newFileName);
            }
            //Pour gerer le username

            $user = $repo->getUser(trim($_POST['username']));
            $username = $user->getUsername();
            $idTouite = $repo->addTouite($username, $texte, $id_img);

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
                    <div class="flex items-center gap-3 mt-3">
                    <label for="img" class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold
                  text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-slate-900"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-5">
            <path d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Zm10.5-11.25h.008v.008h-.008V9.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
        </svg>Ajouter une image <span class="text-gray-500">(max 20 Mo)</span></label>
         <span id="nom-fichier" class="truncate text-sm text-gray-500">Aucune image choisie</span>
                    </div>
                    <input id="img" type="file" name="img" accept="image/png, image/jpeg, image/jpg, image/webp" class="sr-only" onchange="document.getElementById('nom-fichier').textContent = this.files[0]?.name ?? 'Aucune image choisie'"><br>
                </div>
                <br>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-full hover:bg-blue-600">Touiter</button>
                <a href="?action=default" class="px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-full hover:bg-gray-600">Annuler</a>
            </form>
        </section>
        HTML;
    }
}
