DROP TABLE IF EXISTS evaluation;
DROP TABLE IF EXISTS abonnement_tag;
DROP TABLE IF EXISTS abonnement_user;
DROP TABLE IF EXISTS touite_tag;
DROP TABLE IF EXISTS touite;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS image;
DROP TABLE IF EXISTS utilisateur;

CREATE TABLE utilisateur (
id_user INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(30) NOT NULL UNIQUE,
nom_user VARCHAR(50),
prenom_user VARCHAR(50),
email_user VARCHAR(120),
nb_touites INT DEFAULT 0 NOT NULL,
CONSTRAINT chk_user_nb_touites CHECK (nb_touites >= 0)
);


CREATE TABLE image (
id_image INT AUTO_INCREMENT PRIMARY KEY,
desc_image VARCHAR(255),
chemin_img VARCHAR(255) NOT NULL
);


CREATE TABLE tag (
id_tag INT AUTO_INCREMENT PRIMARY KEY,
libelle_tag VARCHAR(50) NOT NULL UNIQUE,
desc_tag VARCHAR(255)
);


CREATE TABLE touite (
id_touite INT AUTO_INCREMENT PRIMARY KEY,
texte_touite VARCHAR(235) NOT NULL,
id_user INT NOT NULL,
id_image INT,
date_touite DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
score INT DEFAULT 0,
CONSTRAINT fk_touite_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_touite_image FOREIGN KEY (id_image) REFERENCES image(id_image)
);


CREATE TABLE touite_tag (
id_touite INT NOT NULL,
id_tag INT NOT NULL,
PRIMARY KEY (id_touite, id_tag),
CONSTRAINT fk_tt_touite FOREIGN KEY (id_touite) REFERENCES touite(id_touite),
CONSTRAINT fk_tt_tag FOREIGN KEY (id_tag) REFERENCES tag(id_tag)
);


CREATE TABLE abonnement_user (
id_follower INT NOT NULL,
id_followed INT NOT NULL,
PRIMARY KEY (id_follower, id_followed),
CONSTRAINT fk_ab_follower FOREIGN KEY (id_follower) REFERENCES utilisateur(id_user),
CONSTRAINT fk_ab_followed FOREIGN KEY (id_followed) REFERENCES utilisateur(id_user),
CONSTRAINT chk_no_self_follow CHECK (id_follower <> id_followed)
);



CREATE TABLE abonnement_tag (
id_user INT NOT NULL,
id_tag INT NOT NULL,
PRIMARY KEY (id_user, id_tag),
CONSTRAINT fk_at_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_at_tag FOREIGN KEY (id_tag) REFERENCES tag(id_tag)
);



CREATE TABLE evaluation (
id_user INT NOT NULL,
id_touite INT NOT NULL,
valeur TINYINT NOT NULL,
PRIMARY KEY (id_user, id_touite),
CONSTRAINT chk_valeur_rating CHECK (valeur IN (-1, 1)),
CONSTRAINT fk_eval_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_eval_touite FOREIGN KEY (id_touite) REFERENCES touite(id_touite)
);



INSERT INTO `utilisateur` (`id_user`, `username`, `nom_user`, `prenom_user`, `email_user`, `nb_touites`) VALUES
(1, 'quent_in', 'Leger', 'Quentin', 'quentin.leger@mail.fr', 2),
(2, 'jdupont', 'Dupont', 'Jean', 'jean.dupont@mail.fr', 1),
(3, 'alice_b', 'Bernard', 'Alice', 'alice.bernard@mail.fr', 0),
(4, 'lucas_m', 'Martin', 'Lucas', 'lucas.martin@mail.fr', 5)
ON DUPLICATE KEY UPDATE 
username=VALUES(username),
nom_user=VALUES(nom_user),
prenom_user=VALUES(prenom_user),
email_user=VALUES(email_user),
nb_touites=VALUES(nb_touites);


INSERT INTO `tag` (`id_tag`, `libelle_tag`, `desc_tag`) VALUES
(1, 'dev', 'Discussions autour du dev et du code'),
(2, 'php', 'Astuces PDO, Composer et OOP'),
(3, 'docker', 'Conteneurs et microservices'),
(4, 'iut', 'Vie de promo et projets universitaires'),
(5, 'cours', 'Révisions et comptes-rendus')
ON DUPLICATE KEY UPDATE
libelle_tag=VALUES(libelle_tag),
desc_tag=VALUES(desc_tag);




-- 1. Insertion des Touites
DROP TABLE IF EXISTS evaluation;
DROP TABLE IF EXISTS abonnement_tag;
DROP TABLE IF EXISTS abonnement_user;
DROP TABLE IF EXISTS touite_tag;
DROP TABLE IF EXISTS touite;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS image;
DROP TABLE IF EXISTS utilisateur;

CREATE TABLE utilisateur (
id_user INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(30) NOT NULL UNIQUE,
nom_user VARCHAR(50),
prenom_user VARCHAR(50),
email_user VARCHAR(120),
nb_touites INT DEFAULT 0 NOT NULL,
CONSTRAINT chk_user_nb_touites CHECK (nb_touites >= 0)
);

CREATE TABLE image (
id_image INT AUTO_INCREMENT PRIMARY KEY,
desc_image VARCHAR(255),
chemin_img VARCHAR(255) NOT NULL
);

CREATE TABLE tag (
id_tag INT AUTO_INCREMENT PRIMARY KEY,
libelle_tag VARCHAR(50) NOT NULL UNIQUE,
desc_tag VARCHAR(255)
);

CREATE TABLE touite (
id_touite INT AUTO_INCREMENT PRIMARY KEY,
texte_touite VARCHAR(235) NOT NULL,
id_user INT NOT NULL,
id_image INT,
date_touite DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
score INT DEFAULT 0,
CONSTRAINT fk_touite_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_touite_image FOREIGN KEY (id_image) REFERENCES image(id_image)
);

CREATE TABLE touite_tag (
id_touite INT NOT NULL,
id_tag INT NOT NULL,
PRIMARY KEY (id_touite, id_tag),
CONSTRAINT fk_tt_touite FOREIGN KEY (id_touite) REFERENCES touite(id_touite),
CONSTRAINT fk_tt_tag FOREIGN KEY (id_tag) REFERENCES tag(id_tag)
);

CREATE TABLE abonnement_user (
id_follower INT NOT NULL,
id_followed INT NOT NULL,
PRIMARY KEY (id_follower, id_followed),
CONSTRAINT fk_ab_follower FOREIGN KEY (id_follower) REFERENCES utilisateur(id_user),
CONSTRAINT fk_ab_followed FOREIGN KEY (id_followed) REFERENCES utilisateur(id_user),
CONSTRAINT chk_no_self_follow CHECK (id_follower <> id_followed)
);

CREATE TABLE abonnement_tag (
id_user INT NOT NULL,
id_tag INT NOT NULL,
PRIMARY KEY (id_user, id_tag),
CONSTRAINT fk_at_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_at_tag FOREIGN KEY (id_tag) REFERENCES tag(id_tag)
);

CREATE TABLE evaluation (
id_user INT NOT NULL,
id_touite INT NOT NULL,
valeur TINYINT NOT NULL,
PRIMARY KEY (id_user, id_touite),
CONSTRAINT chk_valeur_rating CHECK (valeur IN (-1, 1)),
CONSTRAINT fk_eval_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_eval_touite FOREIGN KEY (id_touite) REFERENCES touite(id_touite)
);

-- Utilisateurs avec leur nombre réel de touites
INSERT INTO `utilisateur` (`id_user`, `username`, `nom_user`, `prenom_user`, `email_user`, `nb_touites`) VALUES
(1, 'quent_in', 'Leger', 'Quentin', 'quentin.leger@mail.fr', 6),
(2, 'jdupont', 'Dupont', 'Jean', 'jean.dupont@mail.fr', 5),
(3, 'alice_b', 'Bernard', 'Alice', 'alice.bernard@mail.fr', 5),
(4, 'lucas_m', 'Martin', 'Lucas', 'lucas.martin@mail.fr', 5)
ON DUPLICATE KEY UPDATE
username=VALUES(username),
nom_user=VALUES(nom_user),
prenom_user=VALUES(prenom_user),
email_user=VALUES(email_user),
nb_touites=VALUES(nb_touites);

INSERT INTO `tag` (`id_tag`, `libelle_tag`, `desc_tag`) VALUES
(1, 'dev', 'Discussions autour du dev et du code'),
(2, 'php', 'Astuces PDO, Composer et OOP'),
(3, 'docker', 'Conteneurs et microservices'),
(4, 'iut', 'Vie de promo et projets universitaires'),
(5, 'cours', 'Revisions et comptes-rendus')
ON DUPLICATE KEY UPDATE
libelle_tag=VALUES(libelle_tag),
desc_tag=VALUES(desc_tag);

-- 21 Touites pour tester le découpage par lot de 20
INSERT INTO `touite` (`id_touite`, `texte_touite`, `id_user`, `id_image`, `date_touite`, `score`) VALUES
(1, 'Premier touite sur la plateforme ! Tout tourne sous Docker.', 1, NULL, '2026-11-08 10:00:00', 3),
(2, 'Les controleurs et le dispatcher PSR fonctionnent enfin.', 1, NULL, '2026-11-08 10:30:00', 2),
(3, 'Pensez a bien faire composer dump-autoload apres avoir cree vos classes.', 2, NULL, '2026-11-08 11:15:00', 4),
(4, 'Est-ce que le systeme d''abonnement aux tags est valide ?', 4, NULL, '2026-11-08 11:45:00', -1),
(5, 'Nouvelle seance de refactoring du code pour respecter la separation MVC.', 3, NULL, '2026-11-08 12:10:00', 1),
(6, 'Attention a bien typer les arguments de vos constructeurs en PHP 8 !', 1, NULL, '2026-11-08 13:00:00', 2),
(7, 'Les requetes preparees avec PDO nous sauvent pas mal de galeres d''injection.', 2, NULL, '2026-11-08 13:40:00', 0),
(8, 'Ajout des verifications sur la longueur maximale des messages (235 char).', 3, NULL, '2026-11-08 14:15:00', 3),
(9, 'La maquette en HTML/CSS commence a ressembler a une vraie appli.', 4, NULL, '2026-11-08 14:50:00', 2),
(10, 'Qui est chaud pour tester la methode getTouiteAbonnementPage ce soir ?', 1, NULL, '2026-11-08 15:20:00', 1),
(11, 'Pratique le mot-cle DISTINCT dans le COUNT pour eviter les doublons avec les tags.', 2, NULL, '2026-11-08 16:05:00', 1),
(12, 'Ne pas oublier de documenter les routes dans le README du projet.', 3, NULL, '2026-11-08 16:45:00', 0),
(13, 'Test du systeme d''evaluation avec les valeurs 1 et -1.', 4, NULL, '2026-11-08 17:15:00', -1),
(14, 'Le conteneur MySQL 8.0 consomme tres peu de ressources en local.', 2, NULL, '2026-11-08 18:00:00', 2),
(15, 'Mise en place de la methode getTouitesPages avec LIMIT et OFFSET.', 1, NULL, '2026-11-08 18:30:00', 4),
(16, 'Les tests unitaires passent tous au vert sur le repository.', 3, NULL, '2026-11-08 19:10:00', 2),
(17, 'Toujours verifier que le fichier config.ini est bien exclu du .gitignore.', 4, NULL, '2026-11-08 19:45:00', 1),
(18, 'Bien pratique le trim() sur les chaines pour eviter les touites vides.', 2, NULL, '2026-11-08 20:20:00', 0),
(19, 'Rendu compact valide, reste a peaufiner le rendu long pour le detail.', 3, NULL, '2026-11-08 21:00:00', 2),
(20, '20e touite enregistre ! C''est le dernier visible sur la premiere page.', 4, NULL, '2026-11-08 21:30:00', 5),
(21, '21e touite ! Si tu me lis, c''est que ta pagination fonctionne parfaitement.', 1, NULL, '2026-11-08 22:00:00', 3)
ON DUPLICATE KEY UPDATE
texte_touite=VALUES(texte_touite),
id_user=VALUES(id_user),
id_image=VALUES(id_image),
date_touite=VALUES(date_touite),
score=VALUES(score);

-- 2. Liaison Touite <-> Tag
INSERT INTO `touite_tag` (`id_touite`, `id_tag`) VALUES
(1, 1),
(1, 4),
(2, 1),
(2, 2),
(3, 1),
(3, 3),
(4, 4),
(4, 5)
ON DUPLICATE KEY UPDATE
id_touite=VALUES(id_touite),
id_tag=VALUES(id_tag);

-- 3. Abonnements Utilisateurs aux Tags
INSERT INTO `abonnement_tag` (`id_user`, `id_tag`) VALUES
(1, 1),
(1, 2),
(2, 3),
(4, 4)
ON DUPLICATE KEY UPDATE
id_user=VALUES(id_user),
id_tag=VALUES(id_tag);

-- 4. Évaluations des touites par les utilisateurs
INSERT INTO `evaluation` (`id_user`, `id_touite`, `valeur`) VALUES
(2, 1, 1),
(3, 1, 1),
(4, 1, 1),
(1, 2, 1),
(3, 2, 1),
(2, 3, 1),
(4, 3, 1),
(1, 4, -1),
(1, 20, 1),
(2, 20, 1),
(3, 20, 1),
(2, 21, 1),
(3, 21, 1)
ON DUPLICATE KEY UPDATE
valeur=VALUES(valeur);

-- 5. Ajout abo aux autres users
INSERT INTO `abonnement_user` (`id_follower`, `id_followed`) VALUES
(1, 3),
(1, 2),
(2, 3),
(4, 3),
(2,1)