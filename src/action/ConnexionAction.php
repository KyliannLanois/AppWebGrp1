<?php

namespace touiteur\action;

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
            <h2>Saisissez votre pseudonyme</h2>
            <form method="POST" action="?action=connexion">
                <div>
                    <label for="username">Rentrer ci-dessous votre pseudo : </label><br>
                    <input type="text" id="username" name="username" maxlength="50" value="{$safeText}"><br>
                </div>
                <br>
                <button type="submit">Enregistrer</button>
                <a href="?action=default">Annuler</a>
                <p>Le but de se connecter est de pouvoir sauvegarder votre pseudonyme pour accéder à votre page et touiter plus facilement.</p>
            </form>
        </section>
        HTML;
    }
}