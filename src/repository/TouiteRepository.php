<?php

namespace touiteur\repository;


use Exception;
use PDO;

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
        u.id_user,u.nom_user,u.prenom_user,u.email_user,
        i.chemin_img
        FROM touite t
        JOIN utilisateur u ON u.id_user = t.id_user
        LEFT JOIN image i ON t.id_image = i.id_image
        GROUP BY t.id_touite, t.texte_touite, t.date_touite,u.nom_user,u.prenom_user,u.email_user,i.chemin_img
        ORDER BY t.date_touite DESC";

        $stmt =     $this->pdo->query($sql);
        return $stmt->fetchAll();
    }  //TODO rajouter le systeme d'évaluation




}
