<?php

namespace touiteur\repository;

use Exception;
use PDO;
use touiteur\objet\Image;
use touiteur\objet\Tag;
use touiteur\objet\Touite;
use touiteur\objet\User;

/**
 * Dépôt centralisant l'accès aux données liées aux touites, aux tags et aux abonnements.
 *
 * Cette classe encapsule la connexion PDO et fournit les opérations de persistance
 * nécessaires à la consultation, la création et l'association des entités principales
 * de l'application.
 *
 * La configuration de la base de données doit être chargée avant l'instanciation du dépôt.
 * L'accès peut être mutualisé dans l'application via l'instance unique retournée par
 * la méthode dédiée.
 *
 * Les méthodes de création valident les données reçues et peuvent lever une exception
 * lorsque les contraintes métier ou les préconditions d'accès à la base ne sont pas
 * respectées.
 *
 * @throws Exception Lorsque la configuration de la base de données n'a pas été chargée
 *                   ou lorsque la connexion ne peut pas être initialisée.
 */
class TouiteRepository {

    private static ?array $config= null;
    private static ?TouiteRepository $instance=null;
    private PDO $pdo;

    // Nombre de touites par page (utilisé pour la pagination partout dans l'appli)
    public const LIMITE = 20;

    /***
     * Initialise la connexion à la base de données
     * @throws Exception
     */
    public function __construct (){
        if (self::$config === null){
            throw new Exception("La config n'a pas été chargée");
        }

        $driver = self::$config['driver'] ?? 'mysql';
        $host = self::$config['host'] ?? 'localhost';
        $database = self::$config['database'] ?? '';
        $user = self::$config['username'] ?? '';
        $pass = self::$config['password'] ?? '';

        $dsn = "{$driver}:host={$host};dbname={$database};charset=utf8";

        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    /***
     * Initialise la configuration de la base de données
     * @param String $file
     * @return void
     * @throws Exception
     */
    public static function setConfig (String $file): void{
        $conf = parse_ini_file($file);
        if ($conf === false){
            throw new Exception("Impossible de lire le fichier de configuratino");
        }
        self::$config = $conf;
    }

    /***
     * Utilisé partout dans l'application, renvoie une instance de TouiteRepository avec la configuration de la base de données et connection.
     * @return TouiteRepository
     */
    public static function getInstance() : TouiteRepository{
        if (self::$instance === null){
            self::$instance = new TouiteRepository();
        }
        return self::$instance;
    }


    /***
     * Renvoie toutes les touites avec pagination et ordre chronologique ou anti chronologique.
     * @param int $page la page à afficher
     * @param string $ordre ordre chrono ou anti chrono
     * @return array
     */
    public function getAllTouites(int $page = 1, string $ordre = 'desc'): array{
        // recuperation des données pour l'affichage des touites
        $page = max(1, $page);
        $offset = ($page - 1) * self::LIMITE;
        $direction = (strtolower($ordre) === 'asc') ? 'ASC' : 'DESC';

        //requete
        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite, t.id_image,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img, t.score
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img, t.score
            ORDER BY t.date_touite $direction, t.id_touite $direction
            LIMIT :limit OFFSET :offset";

        //execution de la requete
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', self::LIMITE, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return ($this->getTouites($rows));
    }

    /***
     * Ajoute un touite à la base de données
     * @param string $username
     * @param string $texte
     * @param int|null $id_image
     * @return string
     */
    public function addTouite(string $username, string $texte, ?int $id_image = null): string{
        //validation des données
        $texte = trim($texte);
        if ($texte === '' || mb_strlen($texte) > 235) {
            throw new \InvalidArgumentException("Le touite doit faire entre 1 et 235 caractères");
        }

        //requete
        $sql = "SELECT id_user FROM utilisateur WHERE username = :username";
        $stmt0 = $this->pdo->prepare($sql);
        $stmt0->execute(['username' => $username]);

        $id_user = $stmt0->fetchColumn();
        if ($id_user === false){
            // dans le cas ou la création auto de l'user est un echec
            throw new \InvalidArgumentException("Le utilisateur n'existe pas");
        }

        //execution de la requete
        $sql = "INSERT INTO touite (id_user, texte_touite, id_image) VALUES (:id_user, :texte_touite, :id_image)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_user' => $id_user,
            'texte_touite' => $texte,
            'id_image' => $id_image
        ]);

        $id = $this->pdo->lastInsertId();

        //mise à jour du nombre de touites de l'utilisateur
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET nb_touites = nb_touites + 1 WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);

        return $id;
    }

    /***
     * Ajoute un tag donné dans la base de données
     * @param string $libelle_tag
     * @param string $description
     * @return int
     */
    public function addTag(string $libelle_tag,string $description): int{
        // recupération et validation des données
        $libelle_tag = trim($libelle_tag);
        $description = trim($description);
        if($libelle_tag === ''|| mb_strlen($libelle_tag) > 50){
            throw new \InvalidArgumentException("Le libelle ne doit pas dépasser 50 caracteres");
        }
        if ($description === '' || mb_strlen($description) > 255){
            throw new \InvalidArgumentException("La description ne doit pas dépasser 255 caracteres");
        }

        $Tag = $this->getTag($libelle_tag);

        // Si le tag existe déjà, retourner son ID
        if ($Tag !== null) {
            $id = $Tag->getIdTag();
            return $id;
        }
        // sinon ajout du tag
        $sql = "INSERT INTO tag (libelle_tag, desc_tag) VALUES (:libelle_tag, :desc_tag)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'libelle_tag' => $libelle_tag,
            'desc_tag' => $description
        ]);
        $id = $this->pdo->lastInsertId();
        return $id;
    }


    /***
     * Récupère un tag par son libellé
     * @param string $libelle_tag
     * @return Tag|null
     */
    public function getTag(string $libelle_tag): ?Tag
    {
        $querySelect = "SELECT * FROM tag WHERE libelle_tag = :libelle_tag";
        $stmtSelect = $this->pdo->prepare($querySelect);
        $stmtSelect->execute([':libelle_tag' => $libelle_tag]);
        $tag = $stmtSelect->fetch();

        if ($tag) {
            return new Tag((int)$tag['id_tag'], $tag['libelle_tag']);
        }
        return null;
    }


    /***
     * Recupère un tag par son id
     * @param int $id_Tag
     * @return Tag|null
     */
    public function getTagById(int $id_Tag): ?Tag{
        $querySelect = "SELECT * FROM tag WHERE id_tag = :id_tag";
        $stmtSelect = $this->pdo->prepare($querySelect);
        $stmtSelect->execute([':id_tag' => $id_Tag]);
        $tag = $stmtSelect->fetch();
        if ($tag) {
            return new Tag((int)$tag['id_tag'], $tag['libelle_tag']);
        }
        return null;
    }

    /***
     * Associe un tag à un utilisateur (abonnement)
     * @param int $id_tag
     * @param int $id_user
     * @return bool
     */
    public function linkTagUser(int $id_tag, int $id_user):bool{
        //test sur les données avant de les modifier
        $sql = "SELECT COUNT(*) FROM abonnement_tag 
            WHERE id_user = :id_user AND id_tag = :id_tag";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_user' => $id_user,
            'id_tag' => $id_tag
        ]);

        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        // si tout est ok, on peut ajouter l'abonnement
        $sql = "INSERT INTO abonnement_tag (id_user, id_tag) 
            VALUES (:id_user, :id_tag)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_user' => $id_user,
            'id_tag' => $id_tag
        ]);

        return true;
    }

    /***
     * Associe un tag à une touite
     * @param int $id_touite
     * @param int $id_tag
     * @return bool
     */
    public function linkTouiteTag(int $id_touite, int $id_tag):bool{
        $sql = "SELECT COUNT(*) FROM touite_tag 
            WHERE id_touite = :id_touite AND id_tag = :id_tag";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_touite' => $id_touite,
            'id_tag' => $id_tag
        ]);

        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        $sql = "INSERT INTO touite_tag (id_touite, id_tag) 
            VALUES (:id_touite, :id_tag)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_touite' => $id_touite,
            'id_tag' => $id_tag
        ]);

        return true;
    }

    /***
     * Ajoute un utilisateur à la base de données
     * @param string $username
     * @param string|null $nom_user
     * @param string|null $prenom_user
     * @param string|null $email_user
     * @return string
     */
    public function addUser(string $username,?string $nom_user = null,?string $prenom_user=null,?string $email_user=null):string{
        $username = trim($username);
        $nom_user = $nom_user !== null ? trim($nom_user) : null;
        $prenom_user = $prenom_user !== null ? trim($prenom_user) : null;
        $email_user = $email_user !== null ? trim($email_user) : null;

        if($email_user !== null && !filter_var($email_user, FILTER_VALIDATE_EMAIL)){
            throw new \InvalidArgumentException("L'adresse email n'est pas valide");
        }
        if($nom_user === ''){
            throw new \InvalidArgumentException("Le nom est obligatoire");
        }
        if($prenom_user === ''){
            throw new \InvalidArgumentException("Le prenom est obligatoire");
        }
        if($username === ''){
            throw new \InvalidArgumentException("Le nom d'utilisateur est obligatoire");
        }

        $sql = "INSERT INTO utilisateur (username,nom_user,prenom_user,email_user) VALUES (:username,:nom_user, :prenom_user, :email_user)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'nom_user' => $nom_user,
            'prenom_user' => $prenom_user,
            'email_user' => $email_user
        ]);
        $id = $this->pdo->lastInsertId();
        return $id;
    }

    /***
     * Recupere un utilisateur à partir de son nom d'utilisateur
     * @param string $username
     * @return User
     */
    public function getUser(string $username):User{
        $sql = "SELECT * FROM utilisateur WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        if($row == false){
            $idNewUser = (int)$this->addUser($username);
            return new User($idNewUser, 0, $username);
        }
        $user = new User(
            (int)$row['id_user'],
            0,
            $row['username'],
            $row['nom_user'],
            $row['prenom_user'],
            $row['email_user']
        );
        return $user;
    }

    /***
     * Recupere la liste des touites d'un utilisateur à partir de son id
     * @param int $iduser
     * @return array
     */
    public function getListTouiteUser(int $iduser): array
    {

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,t.id_image,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img, t.score
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            WHERE t.id_user = :iduser
            GROUP BY t.id_touite, t.texte_touite, t.date_touite,t.id_image, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img, t.score
            ORDER BY t.date_touite DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'iduser' => $iduser
        ]);
        $rows = $stmt->fetchAll();


        return ($this->getTouites($rows));
    }


    /***
     * Recupere la liste des touites d'un utilisateur à partir de son id mais renvois les touites dans l'ordre antihorraire.
     * @param int $iduser
     * @return array
     */
    public function getListTouiteUserInverse(int $iduser): array
    {

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,t.id_image,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img, t.score
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            WHERE t.id_user = :iduser
            GROUP BY t.id_touite, t.texte_touite, t.date_touite,t.id_image, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img, t.score
            ORDER BY t.date_touite ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'iduser' => $iduser
        ]);
        $rows = $stmt->fetchAll();


        return ($this->getTouites($rows));
    }

    /***
     * Recupère la liste des touites lié à un tag ou utilisateur suivis par l'utilisateur donné en paramètre (par id))
     * @param int $iduser
     * @param int $page
     * @return array
     */
    public function getTouiteAbonnement(int $iduser, int $page = 1) : array{
        $page = max(1, $page);
        $offset = ($page - 1) * self::LIMITE;

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,t.id_image,
            u.id_user, u.username, u.nom_user, u.prenom_user, u.email_user,
            i.chemin_img, t.score
            FROM touite t
            INNER JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            WHERE t.id_user IN (SELECT id_followed FROM abonnement_user WHERE id_follower = :iduser1)
               OR t.id_touite IN (SELECT tt.id_touite
                                  FROM touite_tag tt
                                  INNER JOIN abonnement_tag at ON at.id_tag = tt.id_tag
                                  WHERE at.id_user = :iduser2)
            ORDER BY t.date_touite DESC, t.id_touite DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':iduser1', $iduser, PDO::PARAM_INT);
        $stmt->bindValue(':iduser2', $iduser, PDO::PARAM_INT);
        $stmt->bindValue(':limit', self::LIMITE, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return $this->getTouites($rows);
    }


    /***
     * Recupère la liste des touites lié à un tag avec son id passé en param
     * @param int $id_tag
     * @return array
     */
    public function getListTouiteTag(int $id_tag): array
    {

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,t.id_image,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img FROM touite t
                INNER JOIN touite_tag tt ON tt.id_touite = t.id_touite
                INNER JOIN tag tag ON tt.id_tag = tag.id_tag
                INNER JOIN utilisateur u ON t.id_user = u.id_user
                LEFT JOIN image i ON t.id_image = i.id_image
                WHERE tag.id_tag = :id_tag
                GROUP BY t.id_touite, t.texte_touite, t.date_touite,t.id_image, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img, t.score
                ORDER BY t.date_touite DESC";



        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_tag' => $id_tag
        ]);
        $rows = $stmt->fetchAll();

        return ($this->getTouites($rows));
    }

    /***
     * Recupère un touite avec son id passé en param
     * @param array $rows
     * @return array
     */
    public function getTouites(array $rows):array{
        $touites = [];

        foreach ($rows as $row) {
            $auteur = new User(
                (int)$row['id_user'],
                0,
                $row['username'],
                $row['nom_user'],
                $row['prenom_user'],
                $row['email_user']
            );

            $img = null;
            if($row['id_image'] !== null){
                $img = $this->getImg((int)$row['id_image']);
            }

            $touites[] = new Touite(
                (int)$row['id_touite'],
                $auteur,
                $row['date_touite'],
                $row['texte_touite'],
                $img,
                [], // ou null selon ce que tu veux initialiser
                $row['score'] ?? 0

            );
        }

        return $touites;

    }

    /***
     * Recupère un utilisateur avec son id passé en param
     * @param string $id_user
     * @return User
     */
    public function getUserById(string $id_user):User{
        $sql = "SELECT * FROM utilisateur WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        $row = $stmt->fetch();

        $user = new User(
            (int)$row['id_user'],
            0,
            $row['username'],
            $row['nom_user'],
            $row['prenom_user'],
            $row['email_user']
        );
        return $user;
    }

    public function getTouiteById(int $id_touite):Touite{
        $sql = "SELECT * FROM touite WHERE id_touite = :id_touite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_touite' => $id_touite]);
        $row = $stmt->fetch();

        $user = $this->getUserById((int)$row['id_user']);
        $img = null;
        if($row['id_image'] !== null){
            $img = $this->getImg((int)$row['id_image']);
        }


        $touite = new Touite(
            (int)$row['id_touite'],
            $user,
            $row['date_touite'],
            $row['texte_touite'],
            $img,
            [], // ou null selon ce que tu veux initialiser
            $row['score'] ?? 0
        );


        return $touite;
    }

    /***
     * Recupère le score d'un utilisateur avec son id passé en param
     * @param $idUser
     * @return int
     */
    public function calculerScore($idUser) : int {
        $sql = "SELECT SUM(score) FROM touite WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $idUser]);
        $row = $stmt->fetch();
        return $row['SUM(score)'] ?? 0;
    }

    /***
     * Permet de faire en sorte que l'utilisateur aime un touite
     * @param int $idTouite
     * @param int $userId
     * @return void
     */
    public function likeTouite(int $idTouite, int $userId): void
    {
        $sql = "SELECT valeur FROM evaluation
        WHERE id_user = :id_user AND id_touite = :id_touite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_user'   => $userId,
            'id_touite' => $idTouite]);

        if ($stmt->rowCount() == 0) {
            $sql = "INSERT INTO evaluation (id_user, id_touite, VALEUR) VALUES (:id_user, :id_touite, 1)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id_user' => $userId, 'id_touite' => $idTouite]);
            $sql = "UPDATE touite SET score = score + 1 WHERE id_touite = :idTouite";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idTouite' => $idTouite]);
        } else {
            $row = $stmt->fetch();

            if ($row['valeur'] == 1) {
                // déjà liké on supprime l'évaluation et on fait -1
                $sql = "DELETE FROM evaluation WHERE id_user = :id_user AND id_touite = :id_touite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id_user' => $userId, 'id_touite' => $idTouite]);
                $sql = "UPDATE touite SET score = score - 1 WHERE id_touite = :idTouite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['idTouite' => $idTouite]);
            } else {
                // avait disliké on passe la valeur à +1 et on fait +2
                $sql = "UPDATE evaluation SET valeur = 1 WHERE id_user = :id_user AND id_touite = :id_touite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id_user' => $userId, 'id_touite' => $idTouite]);
                $sql = "UPDATE touite SET score = score + 2 WHERE id_touite = :idTouite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['idTouite' => $idTouite]);
            }
        }
    }

    /***
     * Permet de faire en sorte que l'utilisateur dislike un touite
     * @param int $idTouite
     * @param int $userId
     * @return void
     */
    public function dislikeTouite(int $idTouite, int $userId): void
    {
        $sql = "SELECT valeur FROM evaluation
        WHERE id_user = :id_user AND id_touite = :id_touite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_user'   => $userId,
            'id_touite' => $idTouite]);

        if ($stmt->rowCount() == 0) {
            $sql = "INSERT INTO evaluation (id_user, id_touite, VALEUR) VALUES (:id_user, :id_touite, -1)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id_user' => $userId, 'id_touite' => $idTouite]);
            $sql = "UPDATE touite SET score = score - 1 WHERE id_touite = :idTouite";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idTouite' => $idTouite]);
        } else {
            $row = $stmt->fetch();

            if ($row['valeur'] == -1) {
                // déjà disliké on supprime l'évaluation et on fait +1
                $sql = "DELETE FROM evaluation WHERE id_user = :id_user AND id_touite = :id_touite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id_user' => $userId, 'id_touite' => $idTouite]);
                $sql = "UPDATE touite SET score = score + 1 WHERE id_touite = :idTouite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['idTouite' => $idTouite]);
            } else {
                // avait liké on passe la valeur à -1 et on fait -2
                $sql = "UPDATE evaluation SET valeur = -1 WHERE id_user = :id_user AND id_touite = :id_touite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id_user' => $userId, 'id_touite' => $idTouite]);
                $sql = "UPDATE touite SET score = score - 2 WHERE id_touite = :idTouite";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['idTouite' => $idTouite]);
            }
        }
    }

    /***
     * Abonne un utilisateur à un autre
     * @param int $idFollower
     * @param int $idFollowed
     * @return void
     */
    public function abonnerUser(int $idFollower,int $idFollowed) : void{
        if ($idFollower == $idFollowed) {
            return;
        }
        $sql = "SELECT * FROM abonnement_user WHERE id_follower = :idFollower AND id_followed = :idFollowed";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idFollower' => $idFollower, 'idFollowed' => $idFollowed]);
        $row = $stmt->fetch();

        if (!$row) {
            $sql = "INSERT INTO abonnement_user (id_follower, id_followed) VALUES (:idFollower, :idFollowed)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idFollower' => $idFollower, 'idFollowed' => $idFollowed]);
        } else {
            $sql = "DELETE FROM abonnement_user WHERE id_follower = :idFollower AND id_followed = :idFollowed";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idFollower' => $idFollower, 'idFollowed' => $idFollowed]);
        }
    }

    /***
     * Abonne un utilisateur à un tag
     * @param int $idUser
     * @param int $idTag
     * @return void
     */
    public function abonnerTag(int $idUser,int $idTag) : void{
        $sql = "SELECT * FROM abonnement_tag WHERE id_user = :idUser AND id_tag = :idTag";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idUser' => $idUser, 'idTag' => $idTag]);
        $row = $stmt->fetch();

        if (!$row) {
            $sql = "INSERT INTO abonnement_tag (id_user, id_tag) VALUES (:idUser, :idTag)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idUser' => $idUser, 'idTag' => $idTag]);
        } else {
            $sql = "DELETE FROM abonnement_tag WHERE id_user = :idUser AND id_tag = :idTag";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idUser' => $idUser, 'idTag' => $idTag]);
        }
    }

    /***
     * Vérifie si un utilisateur est abonné à un autre
     * @param int $idFollowed
     * @return string
     */

    public function estAbonne(int $idFollowed) : string {
        if(isset($_SESSION['user'])){
            $sql = "SELECT * FROM abonnement_user WHERE id_follower = :idFollower AND id_followed = :idFollowed";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idFollower' => $this->getUser($_SESSION['user'])->getIdUser(), 'idFollowed' => $idFollowed]);
            $row = $stmt->fetch();
            if($row){
                return "abonné(e)";
            }
        }
        return "s'abonner";
    }


    /***
     * Verifie si un utilisateur est abonné à un tag
     * @param int $idTag
     * @return string
     */
    public function suisTag(int $idTag) : string{
        if(isset($_SESSION['user'])){
            $sql = "SELECT * FROM abonnement_tag WHERE id_user = :idUser AND id_tag = :idTag";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['idUser' => $this->getUser($_SESSION['user'])->getIdUser(), 'idTag' => $idTag]);
            $row = $stmt->fetch();
            if($row){
                return "suivis";
            }
        }
        return "suivre";
    }

    /***
     * Récupère le classement des influenceurs par rapport au nombre de followers
     * @return array
     */
    public function classementInfluenceurs(): array{
        $sql = "SELECT u.id_user, u.username, 
       (SELECT COUNT(*) FROM touite t where t.id_user = u.id_user) as nbTouites,
       (SELECT COUNT(*) FROM abonnement_user au where au.id_followed = u.id_user) as nbFollowers
        FROM utilisateur u
        ORDER BY nbFollowers DESC;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();

    }

    /***
     * Récupère le nombre total de touites
     * @return int
     */
    public function countTouites(): int{
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM touite");
        return (int) $stmt->fetchColumn();
    }

    /***
     * Récupère le nombre total de touites abonnées à un utilisateur
     * @param int $id_user
     * @return int
     */
    public function countTouitesAbonnement(int $id_user): int {
        $sql = "SELECT COUNT(*)
            FROM touite t
            WHERE t.id_user IN (SELECT id_followed FROM abonnement_user WHERE id_follower = :iduser1)
               OR t.id_touite IN (SELECT tt.id_touite
                                  FROM touite_tag tt
                                  INNER JOIN abonnement_tag at ON at.id_tag = tt.id_tag
                                  WHERE at.id_user = :iduser2)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'iduser1' => $id_user,
            'iduser2' => $id_user
        ]);
        return (int)$stmt->fetchColumn();
    }


    /***
     * Récupère toutes les personnes abonnées à un utilisateur
     * @param int $id_user
     * @return array
     */
    public function getAllAbonnees(int $id_user): array {
        $sql = "SELECT u.id_user, u.username, u.nom_user, u.prenom_user, u.nb_touites from utilisateur u
        JOIN abonnement_user au ON u.id_user = au.id_follower
        WHERE au.id_followed = :id_user
        ORDER BY u.username ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll();
    }


    /**
     * Calcule le score moyen des touites publiés par un utilisateur.
     *
     * @param int $id_user Identifiant de l'utilisateur concerné.
     * @return float Score moyen des touites de l'utilisateur, arrondi à deux décimales, ou 0.0 si aucun score n'est disponible.
     */
    public function getScoreMoyenUser (int $id_user): float {
        $sql="SELECT AVG(sous_req.score_touite)
        FROM (
            SELECT COALESCE(SUM(e.valeur), 0) AS score_touite
            FROM touite t
            LEFT JOIN evaluation e ON t.id_touite = e.id_touite
            WHERE t.id_user = :id_user
            GROUP BY t.id_touite
        ) AS sous_req";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);

        $moyenne = $stmt->fetchColumn();

        return $moyenne !== null ? round ((float)$moyenne,2) : 0.0;
    }


    /**
     * Récupère la liste des tags les plus mentionnés dans les touites, triés par nombre de mentions décroissant puis par libellé.
     *
     * @return array
     */
    public function getTendances() :array
    {
        $sql = " SELECT t.id_tag, t.libelle_tag, t.desc_tag, COUNT(tt.id_touite) AS nb_mentions
            FROM tag t
            JOIN touite_tag tt ON t.id_tag = tt.id_tag
            GROUP BY t.id_tag, t.libelle_tag, t.desc_tag
            ORDER BY nb_mentions DESC, t.libelle_tag ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }


    /**
     * Ajoute une image avec sa description et son chemin, puis retourne l'identifiant généré.
     * @param string $description
     * @param string $path
     * @return int
     */
    public function addImg(string $description, string $path) : int{
        $sql = "INSERT INTO image (desc_image,chemin_img) 
            VALUES (:desc_image, :chemin_img)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'desc_image' => $description,
            'chemin_img' => $path
        ]);

        $newId = $this->pdo->lastInsertId();

        return $newId;

    }

    /**
     * Récupère l'image correspondant à l'identifiant donné.
     *
     * @param int $id_image Identifiant de l'image à récupérer.
     * @return Image Image associée à l'identifiant fourni.
     */
    public function getImg(int $id_image) : Image{
        $sql = "SELECT * FROM image WHERE id_image = :id_image";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_image' => $id_image]
        );
        $row = $stmt->fetch();
        return new Image($id_image,$row['desc_image'],$row['chemin_img']);
    }

    /**
     * Vérifie si les identifiants fournis correspondent à ceux de l'administrateur configuré.
     *
     * @param string $login Identifiant à vérifier.
     * @param string $password Mot de passe à vérifier.
     * @return bool Retourne true si les identifiants sont valides, false sinon.
     */
    public static function verifierAdmin(string $login, string $password): bool {
        if (self::$config === null) {
            throw new Exception("La config n'a pas été chargée");
        }

        $loginBd = self::$config['username'] ?? '';
        $passBd = self::$config['password'] ?? '';

        if ($loginBd === '') {
            return false;
        }

        return hash_equals($loginBd, $login) && hash_equals($passBd, $password);
    }
}