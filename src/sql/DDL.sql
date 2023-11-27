DROP TABLE IF EXISTS acheter CASCADE;
DROP TABLE IF EXISTS jouer CASCADE;
DROP TABLE IF EXISTS ticket CASCADE;
DROP TABLE IF EXISTS client CASCADE;
DROP TABLE IF EXISTS employee CASCADE;
DROP TABLE IF EXISTS personne CASCADE;
DROP TABLE IF EXISTS portique CASCADE;
DROP TABLE IF EXISTS siege CASCADE;
DROP TABLE IF EXISTS seance CASCADE;
DROP TABLE IF EXISTS salle CASCADE;
DROP TABLE IF EXISTS acteur CASCADE;
DROP TABLE IF EXISTS film CASCADE;
DROP TABLE IF EXISTS cinema CASCADE;


CREATE TABLE cinema (
    id_cinema character(5) NOT NULL PRIMARY KEY,
    telephone character(10) NOT NULL,
    adresse character varying(30) NOT NULL,
    url character varying(30) NOT NULL,
    nom_cinema character varying(20) NOT NULL
);

CREATE TABLE film (
    id_film character(10) NOT NULL PRIMARY KEY,
    titre character varying(20) NOT NULL,
    nation character varying(20) NOT NULL,
    duree character(5) NOT NULL CHECK (Duree ~ '^[0-9]+h[0-5][0-9]m$'),
    metteur_en_scene character varying(20) NOT NULL,
    date_sortie date NOT NULL CHECK (Date_sortie <= CURRENT_DATE)
);

CREATE TABLE acteur (
    id_acteur character(12) NOT NULL PRIMARY KEY,
    nom_acteur character varying(30) NOT NULL,
    prenom_acteur character varying(30) NOT NULL
);

CREATE TABLE salle (
    id_salle character varying(6) NOT NULL PRIMARY KEY,
    nom_salle character(6) NOT NULL,
    active boolean NOT NULL,
    nombre_place integer NOT NULL CHECK (Nombre_place > 0),
    type_projection character varying(10) NOT NULL,
    taille_ecran double precision NOT NULL CHECK (Taille_ecran > 0),
    id_cinema character(5),
    CONSTRAINT salle_id_cinema_fkey FOREIGN KEY (id_cinema) REFERENCES cinema(id_cinema)
);

CREATE TABLE seance (
    id_seance character varying(20) NOT NULL PRIMARY KEY,
    heure_projection TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    langue character varying(10) NOT NULL,
    statut character varying(10),
    prix double precision NOT NULL CHECK (Prix > 0),
    id_film character(10),
    id_salle character varying(6),
    CONSTRAINT seance_id_film_fkey FOREIGN KEY (id_film) REFERENCES film(id_film),
    CONSTRAINT seance_id_salle_fkey FOREIGN KEY (id_salle) REFERENCES salle(id_salle)
);

CREATE TABLE siege (
    id_siege character(7) NOT NULL PRIMARY KEY,
    numero_range integer NOT NULL,
    numero_siege integer NOT NULL,
    type character varying(10) NOT NULL,
    remarques character varying(10),
    statut character varying(10) NOT NULL,
    id_salle character varying(6),
    CONSTRAINT siege_id_salle_fkey FOREIGN KEY (id_salle) REFERENCES salle(id_salle)
);


CREATE TABLE portique (
    id_portique character(12) NOT NULL PRIMARY KEY,
    fabricant character varying(20) NOT NULL,
    date_achat date NOT NULL,
    adresseip character varying(15) NOT NULL,
    statut character varying(15) NOT NULL,
    id_salle character varying(6),
    CONSTRAINT portique_id_salle_fkey FOREIGN KEY (id_salle) REFERENCES salle(id_salle)
);


CREATE TABLE personne (
    id_personne character(11) NOT NULL PRIMARY KEY,
    nom_per character varying(20) NOT NULL,
    prenom_per character varying(20) NOT NULL,
    email_per character varying(30) NOT NULL,
    login_per character varying(20) NOT NULL,
    mot_de_passe_per character varying(256) NOT NULL
);

CREATE TABLE ticket (
    id_ticket character(12) NOT NULL PRIMARY KEY,
    date_creation DATE NOT NULL,
    date_expiration DATE,
    statut_usage character varying(10) NOT NULL,
    remarque character varying(10),
    id_siege character(7),
    CONSTRAINT ticket_id_siege_fkey FOREIGN KEY (id_siege) REFERENCES siege(id_siege)
);

CREATE TABLE client (
    id_client character(11) NOT NULL PRIMARY KEY,
    level character varying(10) NOT NULL,
    carte_credit character varying(30),
    langue_prefere CHAR(2) NOT NULL CHECK (Langue_prefere ~ '^[a-zA-Z]{2}$'),
    date_naissance date NOT NULL,
    CONSTRAINT fk_client_personne FOREIGN KEY (id_client) REFERENCES personne(id_personne)
);

CREATE TABLE employee (
    id_employee character(11) NOT NULL PRIMARY KEY,
    poste character varying(20) NOT NULL,
    niveau character varying(10) NOT NULL,
    id_cinema character(5) NOT NULL,
    CONSTRAINT fk_employee_cinema FOREIGN KEY (id_cinema) REFERENCES cinema(id_cinema),
    CONSTRAINT fk_employee_personne FOREIGN KEY (id_employee) REFERENCES personne(id_personne)
);

CREATE TABLE jouer (
    id_film character(10) NOT NULL,
    id_acteur character(12) NOT NULL,
    PRIMARY KEY (id_film, id_acteur),
    FOREIGN KEY (id_acteur) REFERENCES acteur(id_acteur),
    FOREIGN KEY (id_film) REFERENCES film(id_film)
);

CREATE TABLE acheter (
    id_ticket character(12) NOT NULL,
    id_client character(11) NOT NULL,
    nombre_billet integer NOT NULL,
    prix_total double precision NOT NULL,
    PRIMARY KEY (id_ticket, id_client),
    FOREIGN KEY (id_client) REFERENCES client(id_client),
    FOREIGN KEY (id_ticket) REFERENCES ticket(id_ticket)
);