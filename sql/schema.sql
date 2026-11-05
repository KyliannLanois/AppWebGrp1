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
nom_user VARCHAR(50) NOT NULL,
prenom_user VARCHAR(50) NOT NULL,
email_user VARCHAR(120) NOT NULL UNIQUE,
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
