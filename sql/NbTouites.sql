CREATE OR REPLACE TRIGGER majNbTouite
AFTER INSERT ON touite
FOR EACH ROW
BEGIN
UPDATE utilisateur
SET nb_touites = nb_touites + 1
WHERE id_user = :NEW.id_user;
END;
/
