<?php

namespace touiteur\action;

use touiteur\repository\TouiteRepository;

/**
 * Action responsable de la connexion à l’espace d’administration.
 *
 * Traite les requêtes de connexion administrateur en vérifiant les identifiants
 * fournis via un formulaire. En cas de succès, redirige vers la page
 * d’administration. En cas d’échec ou de champs manquants, retourne un message
 * d’erreur. Pour une requête non soumise, génère le formulaire de connexion.
 */
class ConnectionAdminAction extends Action
{
    public function execute(): string{
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($login) or empty($password)) {
                return "<p> Aucune case ne doit rester vide</p>";
            }
            if ($login == 'root' and $password == 'root'){
                // hardcodé car sur webetu il faudrit rentrer les identifiants de Kyliann, on est conscient qu'il faudrait un vrai systeme d'authentification mais ce n'est pas demandé.

                session_regenerate_id(true);
                $_SESSION['admin'] = true;
                header('Location: ?action=adminPage');
                exit();
            }

            return "<p> Le login et le mot de passe sont incorrects</p>";
        }

        return $this->renderForm('');
    }


    private function renderForm(string $ancientexte): string{
        $safeText = htmlspecialchars($ancientexte);
        return <<< HTML
        <section class="connexion-action">
            <h1 class="text-lg dark:text-gray-400 font-bold">Connexion en tant qu'administrateur</h1>
            <form method="POST" action="?action=connexionAdmin">
                <div class="flex flex-col border border-gray-200 rounded-md p-4 shadow-lg dark:border-gray-400 dark:shadow-gray-600/50 dark:bg-slate-950">
                    <label for="login" class="text-sm dark:text-gray-400">Rentrer ci-dessous le login: </label><br>
                    <input type="text" id="login" name="login" maxlength="50" value="{$safeText}" class="w-full border-0 border-b-2 border-gray-300 bg-transparent px-1 py-2
              focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400 dark:shadow-none dark:text-gray-400"><br>
                    <label for="password" class="text-sm dark:text-gray-400">Rentrer ci-dessous le mot de passe: </label><br>
                    <input type="password" id="password" name="password" maxlength="50" value="{$safeText}" class="w-full border-0 border-b-2 border-gray-300 bg-transparent px-1 py-2
              focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400 dark:shadow-none dark:text-gray-400"><br>
                </div>
                <br>
                <button type="submit" class="bg-blue-500 rounded-full p-2 text-white hover:bg-blue-600 text-center text-lg font-bold transition cursor-pointer mr-5">Enregistrer</button>
                <a href="?action=default" class="rounded-full p-2 font-bold text-lg text-center transition border-2 hover:bg-gray-200 border-gray-200 dark:bg-slate-950 dark:text-gray-400 dark:border-gray-600 dark:shadow-none dark:hover:bg-slate-900">Annuler</a>
                <p class="text-sm text-gray-300 mt-5">La connection admin permet de consulter des données privées.</p>
            </form>
        </section>
        HTML;
    }
}