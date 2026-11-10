<?php

namespace touiteur\action;

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
            <h2>Connexion en tant qu'administrateur</h2>
            <form method="POST" action="?action=connexionAdmin">
                <div>
                    <label for="login">Rentrer ci-dessous le login: </label><br>
                    <input type="text" id="login" name="login" maxlength="50" value="{$safeText}"><br>
                    <label for="password">Rentrer ci-dessous le mot de passe: </label><br>
                    <input type="password" id="password" name="password" maxlength="50" value="{$safeText}"><br>
                </div>
                <br>
                <button type="submit">Enregistrer</button>
                <a href="?action=default">Annuler</a>
                <p>La connection admin permet de consulter des données privées.</p>
            </form>
        </section>
        HTML;
    }
}