<?php

namespace touiteur\repository;



use Exception;
use PDO;
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
                   i.chemin_img
            FROM touite t
            JOIN utilisateur u ON u.id_user = t.id_user
            LEFT JOIN image i ON t.id_image = i.id_image
            GROUP BY t.id_touite, t.texte_touite, t.date_touite, u.id_user,u.username, u.nom_user, u.prenom_user, u.email_user, i.chemin_img
            ORDER BY t.date_touite DESC";

        $stmt =     $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        $touites = [];

        foreach ($rows as $row) {
            $auteur = new User(
                (int)$row['id_user'],
                $row['username'],
                $row['nom_user'],
                $row['prenom_user'],
                $row['email_user'],
                0
            );

            $touites[] = new Touite(
                (int)$row['id_touite'],
                $auteur,
                $row['date_touite'],
                $row['texte_touite'],
                $row['chemin_img'] ?? null,
                [] // ou null selon ce que tu veux initialiser
            );
        }

        return $touites;
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

    public function addTag(string $libelle,string $description): string{
        $libelle = trim($libelle);
        $description = trim($description);
        if($libelle === ''|| mb_strlen($libelle) > 50){
            throw new \InvalidArgumentException("Le libelle ne doit pas dépasser 50 caracteres");
        }
        if ($description === '' || mb_strlen($description) > 255){
            throw new \InvalidArgumentException("La description ne doit pas dépasser 255 caracteres");
        }

        $sql = "INSERT INTO tag (libelle, description) VALUES (:libelle, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'libelle' => $libelle,
            'description' => $description
        ]);
        $id = $this->pdo->lastInsertId();
        return $id;
    }

    public function linkTagUser(int $id_tag,int $id_user){
        $sql = "INSERT INTO abonnement_tag (id_user, id_tag) VALUES (:id_tag, :id_user)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_tag' => $id_tag,
            'id_user' => $id_user
        ]);
    }

    public function linkTouiteTag(int $id_touite,int $id_tag){
        $sql = "INSERT INTO touite_tag (id_touite, id_tag) VALUES (:id_touite, :id_tag)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_touite' => $id_touite,
            'id_tag' => $id_tag
        ]);
    }

    public function addUser(string $username,string $nom_user,string $prenom_user,string $email_user):string{
        $username = trim($username);
        $nom_user = trim($nom_user);
        $prenom_user = trim($prenom_user);
        $email_user = trim($email_user);
        if($email_user === '' || filter_var($email_user, FILTER_VALIDATE_EMAIL)){
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

        }
        $user = new User(
            (int)$row['id_user'],
            $row['username'],
            $row['nom_user'],
            $row['prenom_user'],
            $row['email_user'],
            $row['nb_touites'],
        );
        return $user;
    }


    //TODO rajouter le systeme d'évaluation




}
