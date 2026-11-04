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

$sql = "CREATE OR REPLACE TRIGGER checkFollow
BEFORE UPDATE ON touites
FOR EACH ROW

BEGIN

IF :NEW.id_user = :OLD.id_user THEN
        RAISE_APPLICATION_ERROR(-20001, 'Vous ne pouvez pas modifier le propriétaire d'un touite.');
END IF;
END;
/";

$resultset = $connexion->query($sql);
echo "Trigger ok";
$connexion = null;