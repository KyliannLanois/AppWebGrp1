CREATE TABLE utilisateur (
id_user NUMBER(8),
username VARCHAR2(30) NOT NULL UNIQUE,
nom_user VARCHAR2(50),
prenom_user VARCHAR2(50),
email_user VARCHAR2(120),
nb_touites NUMBER(6) DEFAULT 0 NOT NULL,
PRIMARY KEY (id_user),
CONSTRAINT chk_user_nb_touites CHECK (nb_touites >= 0)
);


CREATE TABLE image (
id_image NUMBER(8),
desc_image VARCHAR2(255),
chemin_img VARCHAR2(255) NOT NULL,
PRIMARY KEY (id_image)
);


CREATE TABLE tag (
id_tag NUMBER(8),
libelle_tag VARCHAR2(50) NOT NULL UNIQUE,
desc_tag VARCHAR2(255),
PRIMARY KEY (id_tag),
CONSTRAINT chk_format_tag CHECK (libelle_tag LIKE '#%')
);


CREATE TABLE touite (
id_touite NUMBER(10),
texte_touite VARCHAR2(235) NOT NULL,
id_user NUMBER(8) NOT NULL,
id_image NUMBER(8),
date_touite DATE DEFAULT SYSDATE NOT NULL,
score NUMBER(6) DEFAULT 0 NOT NULL,
PRIMARY KEY (id_touite),
CONSTRAINT fk_touite_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_touite_image FOREIGN KEY (id_image) REFERENCES image(id_image)
);


CREATE TABLE touite_tag (
id_touite NUMBER(10) NOT NULL,
id_tag NUMBER(8) NOT NULL,
PRIMARY KEY (id_touite, id_tag),
CONSTRAINT fk_tt_touite FOREIGN KEY (id_touite) REFERENCES touite(id_touite),
CONSTRAINT fk_tt_tag FOREIGN KEY (id_tag) REFERENCES tag(id_tag)
);


CREATE TABLE abonnement_user (
id_follower NUMBER(8) NOT NULL,
id_followed NUMBER(8) NOT NULL,
PRIMARY KEY (id_follower, id_followed),
CONSTRAINT fk_ab_follower FOREIGN KEY (id_follower) REFERENCES utilisateur(id_user),
CONSTRAINT fk_ab_followed FOREIGN KEY (id_followed) REFERENCES utilisateur(id_user),
CONSTRAINT chk_no_self_follow CHECK (id_follower <> id_followed)
);


CREATE TABLE abonnement_tag (
id_user NUMBER(8) NOT NULL,
id_tag NUMBER(8) NOT NULL,
PRIMARY KEY (id_user, id_tag),
CONSTRAINT fk_at_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_at_tag FOREIGN KEY (id_tag) REFERENCES tag(id_tag)
);


CREATE TABLE evaluation (
id_user NUMBER(8) NOT NULL,
id_touite NUMBER(10) NOT NULL,
valeur NUMBER(2) NOT NULL,
PRIMARY KEY (id_user, id_touite),
CONSTRAINT chk_valeur_rating CHECK (valeur IN (-1, 1)),
CONSTRAINT fk_eval_user FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
CONSTRAINT fk_eval_touite FOREIGN KEY (id_touite) REFERENCES touite(id_touite)
);