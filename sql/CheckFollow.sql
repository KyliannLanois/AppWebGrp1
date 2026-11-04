CREATE OR REPLACE TRIGGER checkFollow
BEFORE INSERT ON abonnement_user
FOR EACH ROW
DECLARE
em1 utilisateur.email_user%TYPE;
    em2 utilisateur.email_user%TYPE;
BEGIN
SELECT email_user INTO em1 FROM utilisateur WHERE id_user = :NEW.id_follower;
SELECT email_user INTO em2 FROM utilisateur WHERE id_user = :NEW.id_followed;

IF :NEW.id_follower = :NEW.id_followed OR em1 = em2 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Vous ne pouvez pas vous suivre vous-même.');
END IF;
END;
/