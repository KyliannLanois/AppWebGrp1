<?php

namespace touiteur\action;

/**
 * Action de connexion permettant à un utilisateur de renseigner et mémoriser
 * son pseudonyme dans la session.
 *
 * Cette action traite la soumission du formulaire de connexion, valide que le
 * pseudonyme fourni n'est pas vide, l'enregistre en session puis redirige vers
 * l'action par défaut. Lorsqu'aucune soumission n'est effectuée, elle affiche
 * le formulaire prérempli avec le pseudonyme déjà présent en session, le cas
 * échéant.
 */
class ConnexionAction extends Action
{

    public function execute(): string{
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $texte = trim($_POST['username'] ?? '');

            if (empty($texte)) {
                return "<p> Le pseudonyme ne peut etre vide</p>";
            }

            $_SESSION['user'] = $texte;

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
        <section class="connexion-action">
            <h1 class="text-lg dark:text-gray-400 font-bold">Saisissez votre pseudonyme</h1>
            <form method="POST" action="?action=connexion">
                <div class="flex flex-col border border-gray-200 rounded-md p-4 shadow-lg dark:border-gray-400 dark:shadow-gray-600/50 dark:bg-slate-950">
                    <label for="username" class="text-sm dark:text-gray-400">Rentrer ci-dessous votre pseudo : </label><br>
                    <input type="text" id="username" name="username" maxlength="50" value="{$safeText}" class="w-full border-0 border-b-2 border-gray-300 bg-transparent px-1 py-2
              focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400 dark:shadow-none dark:text-gray-400"><br>
                </div>
                <br>
                <button type="submit" class="bg-blue-500 rounded-full p-2 text-white hover:bg-blue-600 text-center text-lg font-bold transition cursor-pointer mr-5">Enregistrer</button>
                <a href="?action=default" class="rounded-full p-2 font-bold text-lg text-center transition border-2 hover:bg-gray-200 border-gray-200 dark:bg-slate-950 dark:text-gray-400 dark:border-gray-600 dark:shadow-none dark:hover:bg-slate-900">Annuler</a>
                <p class="text-sm text-gray-300 mt-5">Le but de se connecter est de pouvoir sauvegarder votre pseudonyme pour accéder à votre page et touiter plus facilement.</p>
            </form>
        </section>
        HTML;
    }
}