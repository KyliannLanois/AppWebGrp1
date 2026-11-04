<?php

$config = parse_ini_file(__DIR__ . '/../config/db.config.ini', true);

if ($config === false) {
    echo ("Fichier de configuration introuvable ou invalide.");
}

$db = $config['database'];

$connexion = new PDO(
    "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
    $db['user'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = "CREATE OR REPLACE TRIGGER majNbTouite
AFTER INSERT ON touite
FOR EACH ROW
BEGIN
UPDATE utilisateur
SET nb_touites = nb_touites + 1
WHERE id_user = :NEW.id_user;
END;
/";

$resultset = $connexion->query($sql);
echo "Trigger ok";
$connexion = null;

