<?php


$config = parse_ini_file(__DIR__ . '/../config/db.config.ini', true);

if ($config === false) {
    echo("Fichier de configuration introuvable ou invalide.");
}

$db = $config['database'];

$connexion = new PDO(
    "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
    $db['user'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = "CREATE OR REPLACE TRIGGER checkTouiteLimite
BEFORE INSERT ON touites
DECLARE 
id utilisateur.id_user%TYPE;
cpt NUMBER;
FOR EACH ROW
BEGIN
id := :NEW.id_user;
SELECT COUNT(*) INTO cpt FROM Touites WHERE id_user = id and TO_CHAR(date_touite, 'YYYY-MM-DD') = TO_CHAR(SYSDATE, 'YYYY-MM-DD');
IF cpt >= 50 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Limite de touites par jour atteinte.');
END IF;
END;
/";

$resultset = $connexion->query($sql);
echo "Trigger ok";
$connexion = null;