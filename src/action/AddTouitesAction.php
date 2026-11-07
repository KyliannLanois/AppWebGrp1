<?php


namespace touiteur\action;

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


            $repo->addTouite($username, $texte);

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
            <h2>Publier un nouveau touite</h2>
            <form method="POST" action="?action=add-touite">
                <div>
                    <label for="username">Rentrer ci-dessous votre pseudo : </label><br>
                    <input type="text" id="username" name="username" maxlength="50" value="{$safeText}"><br>
                    <label for="texte">Votre message (235 caractères max, incluez vos #tags) :</label><br>
                    <textarea id="texte" name="texte" rows="4" cols="50" maxlength="235" required></textarea>
                </div>
                <br>
                <button type="submit">Touiter</button>
                <a href="?action=default">Annuler</a>
            </form>
        </section>
        HTML;
    }
}
