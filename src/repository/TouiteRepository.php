<?php

namespace touiteur\repository;



use Exception;
use PDO;
use touiteur\objet\Tag;
use touiteur\objet\Touite;
use touiteur\objet\User;

class TouiteRepository {

    private static ?array $config= null;
    private static ?TouiteRepository $instance=null;
    private PDO $pdo;

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


    public static function setConfig (String $file): void{
        $conf = parse_ini_file($file);
        if ($conf === false){
            throw new Exception("Impossible de lire le fichier de configuratino");
        }
        self::$config = $conf;
    }


    public static function getInstance() : TouiteRepository{
        if (self::$instance === null){
            self::$instance = new TouiteRepository();
        }
        return self::$instance;
    }


    public function getPDO(): PDO {
        return $this->pdo;
    }




    public function getAllTouites(): array{
        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img, t.score
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img, t.score
            ORDER BY t.date_touite DESC";

        $stmt =     $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        return ($this->getTouites($rows));
    }

    public function getAllTouitesInverse(): array{
        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img, t.score
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img, t.score
            ORDER BY t.date_touite ASC";

        $stmt =     $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        return ($this->getTouites($rows));
    }


    public function addTouite(string $username, string $texte, ?int $id_image = null): string{

        $texte = trim($texte);
        if ($texte === '' || mb_strlen($texte) > 235) {
            throw new \InvalidArgumentException("Le touite doit faire entre 1 et 235 caractères");
        }
        $sql = "SELECT id_user FROM utilisateur WHERE username = :username";
        $stmt0 = $this->pdo->prepare($sql);
        $stmt0->execute(['username' => $username]);

        $id_user = $stmt0->fetchColumn();
        if ($id_user === false){
            throw new \InvalidArgumentException("Le utilisateur n'existe pas");
        }


        $sql = "INSERT INTO touite (id_user, texte_touite, id_image) VALUES (:id_user, :texte_touite, :id_image)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_user' => $id_user,
            'texte_touite' => $texte,
            'id_image' => $id_image
        ]);

        $id = $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare("UPDATE utilisateur SET nb_touites = nb_touites + 1 WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);

        return $id;
    }

    //TODO rajouter le systeme d'évaluation

    public function addTag(string $libelle_tag,string $description): int{
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

    public function linkTagUser(int $id_tag, int $id_user):bool{
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

        $sql = "INSERT INTO abonnement_tag (id_user, id_tag) 
            VALUES (:id_user, :id_tag)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_user' => $id_user,
            'id_tag' => $id_tag
        ]);

        return true;
    }

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

    public function getListTouiteUser(int $iduser): array
    {

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            WHERE t.id_user = :iduser
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img
            ORDER BY t.date_touite DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'iduser' => $iduser
        ]);
        $rows = $stmt->fetchAll();


        return ($this->getTouites($rows));
    }

    public function getListTouiteUserInverse(int $iduser): array
    {

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            WHERE t.id_user = :iduser
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img
            ORDER BY t.date_touite ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'iduser' => $iduser
        ]);
        $rows = $stmt->fetchAll();


        return ($this->getTouites($rows));
    }

    public function getTouiteAbonnement(int $iduser) : array{
        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
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
            ORDER BY t.date_touite DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'iduser1' => $iduser,
            'iduser2' => $iduser
        ]);
        $rows = $stmt->fetchAll();

        return $this->getTouites($rows);
    }










    public function getListTouiteTag(int $id_tag): array ///TODO ajouter les tag (VRM dans la base)
    {



        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
                   u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img FROM touite t
                INNER JOIN touite_tag tt ON t.id_touite = tt.id_touite
                INNER JOIN tag tag ON tt.id_tag = tag.id_tag
                INNER JOIN utilisateur u ON t.id_user = u.id_user
                LEFT JOIN image i ON t.id_image = i.id_image
                WHERE tag.id_tag = :id_tag
                GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img, t.score
                ORDER BY t.date_touite DESC";



        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_tag' => $id_tag
        ]);
        $rows = $stmt->fetchAll();

        return ($this->getTouites($rows));
    }









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

            $touites[] = new Touite(
                (int)$row['id_touite'],
                $auteur,
                $row['date_touite'],
                $row['texte_touite'],
                $row['chemin_img'] ?? null,
                [], // ou null selon ce que tu veux initialiser
                $row['score'] ?? 0

            );
        }

        return $touites;

    }

    public function getUserById(string $id_user):User{
        $sql = "SELECT * FROM utilisateur WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        $row = $stmt->fetch();
        if($row == false){

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

    public function getTouiteById(int $id_touite):Touite{
        $sql = "SELECT * FROM touite WHERE id_touite = :id_touite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_touite' => $id_touite]);
        $row = $stmt->fetch();

        $user = $this->getUserById((int)$row['id_user']);


        $touite = new Touite(
            (int)$row['id_touite'],
            $user,
            $row['date_touite'],
            $row['texte_touite'],
            $row['chemin_img'] ?? null,
            [], // ou null selon ce que tu veux initialiser
        $row['score'] ?? 0
        );


        return $touite;
    }

    public function calculerScore($idUser) : int {
        $sql = "SELECT SUM(score) FROM touite WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $idUser]);
        $row = $stmt->fetch();
        return $row['SUM(score)'] ?? 0;
    }

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

    public function abonnerUser(int $idFollower,int $idFollowed) : void{
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

    public function countTouites(): int{
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM touite");
        return (int) $stmt->fetchColumn();
    }

    public function countTouitesAbonnement(int $id_user): int {
        $sql = "SELECT COUNT(DISTINCT t.id_touite)
            FROM touite t
            LEFT JOIN touite_tag tt ON t.id_touite = tt.id_touite
            WHERE t.id_user IN (SELECT id_followed FROM abonnement_user WHERE id_follower = :id_user)
               OR tt.id_tag IN (SELECT id_tag FROM abonnement_tag WHERE id_user = :id_user)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        return (int)$stmt->fetchColumn();
    }


    public function getTouitesPages(int $page=1, int $limit = 20): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $limit;

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
                   u.id_user, u.nb_touites, u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user, u.nb_touites, u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img
            ORDER BY t.date_touite DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return $this->getTouites($rows);
    }

    public function getTouiteAbonnementPage(int $id_user, int $page = 1, int $limit = 20): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $limit;

        $sql = "SELECT t.id_touite, t.texte_touite, t.date_touite,
                   u.id_user, u.nb_touites, u.username, u.nom_user, u.prenom_user, u.email_user,
                   i.chemin_img
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            LEFT JOIN touite_tag tt ON t.id_touite = tt.id_touite
            WHERE t.id_user IN (SELECT id_followed FROM abonnement_user WHERE id_follower = :id_user)
               OR tt.id_tag IN (SELECT id_tag FROM abonnement_tag WHERE id_user = :id_user)
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user, u.nb_touites, u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img
            ORDER BY t.date_touite DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return $this->getTouites($rows);
    }


    public function getAllAbonnees(int $id_user): array {
        $sql = "SELECT u.id_user, u.username, u.nom_user, u.prenom_user, u.nb_touites from utilisateur u
        JOIN abonnement_user au ON u.id_user = au.id_follower
        WHERE au.id_followed = :id_user
        ORDER BY u.username ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll();
    }


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


    public function getTendances() :array {
        $sql =" SELECT t.id_tag, t.libelle_tag, t.desc_tag, COUNT(tt.id_touite) AS nb_mentions
            FROM tag t
            JOIN touite_tag tt ON t.id_tag = tt.id_tag
            GROUP BY t.id_tag, t.libelle_tag, t.desc_tag
            ORDER BY nb_mentions ASC, t.libelle_tag ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}
