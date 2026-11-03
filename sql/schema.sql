DROP TABLE EVALUATION;
DROP TABLE ABONNEMENT_TAG;
DROP TABLE ABONNEMENT_USER;
DROP TABLE TOUITE_TAG;
DROP TABLE TOUITE;
DROP TABLE TAG;
DROP TABLE IMAGE;
DROP TABLE UTILISATEUR;

CREATE TABLE UTILISATEUR (
    id_user NUMBER PRIMARY KEY,
    nom_user VARCHAR2(50) NOT NULL,
    prenom_user VARCHAR2(50) NOT NULL,
    email_user VARCHAR2(120) NOT NULL UNIQUE,
    nb_touites NUMBER DEFAULT 0 NOT NULL,
    CONSTRAINT chk_user_nb_touites CHECK (nb_touites >= 0)
);

CREATE TABLE IMAGE (
    id_image NUMBER PRIMARY KEY,
    desc_image VARCHAR2(255),
    chemin_img VARCHAR2(255) NOT NULL
);

CREATE TABLE TAG (
    id_tag NUMBER PRIMARY KEY,
    libelle_tag VARCHAR2(50) NOT NULL UNIQUE,
    desc_tag VARCHAR2(255)
);

CREATE TABLE TOUITE (
    id_touite NUMBER PRIMARY KEY,
    texte_touite VARCHAR2(235) NOT NULL,
    id_user NUMBER NOT NULL,
    id_image NUMBER,
    date_touite DATE DEFAULT SYSDATE NOT NULL,
    CONSTRAINT fk_touite_user FOREIGN KEY (id_user) REFERENCES UTILISATEUR(id_user),
    CONSTRAINT fk_touite_image FOREIGN KEY (id_image) REFERENCES IMAGE(id_image)
);

CREATE TABLE TOUITE_TAG (
    id_touite NUMBER NOT NULL,
    id_tag NUMBER NOT NULL,
    CONSTRAINT pk_touite_tag PRIMARY KEY (id_touite, id_tag),
    CONSTRAINT fk_tt_touite FOREIGN KEY (id_touite) REFERENCES TOUITE(id_touite),
    CONSTRAINT fk_tt_tag FOREIGN KEY (id_tag) REFERENCES TAG(id_tag)
);

CREATE TABLE ABONNEMENT_USER (
    id_follower NUMBER NOT NULL,
    id_followed NUMBER NOT NULL,
    CONSTRAINT pk_abonnement_user PRIMARY KEY (id_follower, id_followed),
    CONSTRAINT fk_ab_follower FOREIGN KEY (id_follower) REFERENCES UTILISATEUR(id_user),
    CONSTRAINT fk_ab_followed FOREIGN KEY (id_followed) REFERENCES UTILISATEUR(id_user),
    CONSTRAINT chk_no_self_follow CHECK (id_follower <> id_followed)
);

CREATE TABLE ABONNEMENT_TAG (
    id_user NUMBER NOT NULL,
    id_tag NUMBER NOT NULL,
    CONSTRAINT pk_abonnement_tag PRIMARY KEY (id_user, id_tag),
    CONSTRAINT fk_at_user FOREIGN KEY (id_user) REFERENCES UTILISATEUR(id_user),
    CONSTRAINT fk_at_tag FOREIGN KEY (id_tag) REFERENCES TAG(id_tag)
);

CREATE TABLE EVALUATION (
    id_user NUMBER NOT NULL,
    id_touite NUMBER NOT NULL,
    valeur NUMBER(1) NOT NULL,
    CONSTRAINT pk_evaluation PRIMARY KEY (id_user, id_touite),
    CONSTRAINT chk_valeur_rating CHECK (valeur IN (-1, 1)),
    CONSTRAINT fk_eval_user FOREIGN KEY (id_user) REFERENCES UTILISATEUR(id_user),
    CONSTRAINT fk_eval_touite FOREIGN KEY (id_touite) REFERENCES TOUITE(id_touite)
);