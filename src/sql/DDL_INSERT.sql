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



SET client_encoding = 'UTF8';
--
-- Data for Name: cinema; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO cinema VALUES ('C4013', '0143658799', '3 rue la Feuille', 'cinerama.fr', 'CineRama'),
                        ('C1509', '0177549672', '10 bd Mésange', 'flickflare.fr', 'Flick Flare'),
                        ('C3001', '0145367890', '12 rue Capucines', 'galaxycine.fr', 'GalaxyCine'),
                        ('C3002', '0145367891', '55 bd Voltaire', 'starcinema.fr', 'StarCinema'),
                        ('C3003', '0145367892', '6 av Victor Hugo', 'suncine.fr', 'SunCine');

--
-- Data for Name: acteur; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO acteur VALUES ('A01743268', 'Freeman', 'Morgan'),
                        ('A02334895', 'Pitt', 'Brad'),
                        ('A03412118', 'Johnson', 'Dwayne'),
                        ('A10123456', 'Durand', 'Paul'),
                        ('A10123457', 'Morel', 'Emilie'),
                        ('A10123458', 'Favre', 'Julien'),
                        ('A10123459', 'Lambert', 'Charlotte'),
                        ('A10123460', 'Girard', 'Sophie'),
                        ('A10123461', 'Lopez', 'Alexandre'),
                        ('A10123462', 'Bonnet', 'Thomas'),
                        ('A10123463', 'Francois', 'Laura'),
                        ('A10123464', 'David', 'Marine'),
                        ('A10123465', 'Bertrand', 'Vincent');

--
-- Data for Name: film; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO film VALUES ('F049828617', 'L’échec scolaire', 'France', '1h19m', 'Didier Bezin', '2020-05-09'),
                        ('F984137122', 'Kill Unconfirmed', 'USA', '2h16m', 'Tiago Rodriguez', '2009-01-05'),
                        ('F301928374', 'Nuit Parisienne', 'France', '1h45m', 'Sophie Laurant', '2019-06-15'),
                        ('F302837465', 'Aventures en Asie', 'France', '2h10m', 'Pierre Dupont', '2018-04-20'),
                        ('F303746576', 'Sous le Ciel Bleu', 'USA', '1h55m', 'John Smith', '2020-12-11'),
                        ('F304655687', 'Le Mystère du Nil', 'Egypte', '2h05m', 'Ahmed Zaki', '2021-03-05'),
                        ('F305564798', 'La Forêt Enchantée', 'Canada', '1h30m', 'Emma Brown', '2017-07-21'),
                        ('F306473809', 'Esprit de l‘océan', 'Australie', '1h40m', 'James Wilson', '2019-09-18');

--
-- Data for Name: salle; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO salle VALUES ('S00001', 'Sal01B', true, 100, '3D', 24, 'C1509'),
                        ('S00002', 'Sal02B', false, 220, 'IMAX', 48, 'C1509'),
                        ('S00003', 'Sal03A', true, 150, '4K', 30, 'C1509'),
                        ('S00004', 'Sal04A', false, 200, '2D', 25, 'C3001'),
                        ('S00005', 'Sal05A', true, 120, '3D', 28, 'C3002'),
                        ('S00006', 'Sal06A', true, 100, 'IMAX', 40, 'C3003'),
                        ('S00007', 'Sal07A', false, 110, '4K', 32, 'C3002'),
                        ('S00008', 'Sal08A', true, 130, '2D', 26, 'C3001'),
                        ('S00009', 'Sal09A', false, 140, '3D', 35, 'C3003'),
                        ('S00010', 'Sal10A', true, 180, 'IMAX', 45, 'C4013'),
                        ('S00011', 'Sal11A', true, 160, '4K', 30, 'C3001');

--
-- Data for Name: seance; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO seance VALUES ('Snc21581897', '2023-11-22 14:30:00', 'FR', 'En cours', 9.5, 'F049828617', 'S00001'),
                        ('Snc20139483', '2023-11-24 20:00:00', 'EN', 'A venir', 8.5, 'F984137122', 'S00002'),
                        ('Snc20113483', '2023-11-24 20:00:00', 'FR', 'A venir', 7.5, 'F984137122', 'S00004'),
                        ('Snc31581900', '2023-11-25 14:00:00', 'FR', 'En cours', 10.5, 'F301928374', 'S00003'),
                        ('Snc31581901', '2023-11-27 16:00:00', 'EN', 'A venir', 11, 'F302837465', 'S00004'),
                        ('Snc31581902', '2023-11-28 18:00:00', 'FR', 'En cours', 9.5, 'F303746576', 'S00005'),
                        ('Snc31581903', '2023-11-29 20:00:00', 'EN', 'A venir', 12, 'F304655687', 'S00006'),
                        ('Snc31581904', '2023-12-16 15:30:00', 'FR', 'En cours', 8.5, 'F305564798', 'S00007'),
                        ('Snc31581905', '2023-12-07 17:30:00', 'DE', 'A venir', 10, 'F306473809', 'S00008'),
                        ('Snc31581907', '2023-12-09 21:30:00', 'DE', 'A venir', 11.5, 'F049828617', 'S00010'),
                        ('Snc31581908', '2023-12-01 14:30:00', 'FR', 'En cours', 10.5, 'F302837465', 'S00011'),
                        ('Snc31581909', '2023-12-01 16:30:00', 'DE', 'A venir', 12, 'F302837465', 'S00006');

--
-- Data for Name: siege; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO siege VALUES ('Siel_02', 8, 22, 'Normal', NULL, 'Libre', 'S00001'),
                        ('Siel_03', 2, 15, 'VIP', NULL, 'Occupé', 'S00001'),
                        ('Siel_10', 1, 1, 'Normal', NULL, 'Libre', 'S00003'),
                        ('Siel_11', 1, 2, 'Normal', NULL, 'Libre', 'S00003'),
                        ('Siel_12', 1, 3, 'VIP', NULL, 'Libre', 'S00004'),
                        ('Siel_13', 1, 4, 'VIP', NULL, 'Occupé', 'S00004'),
                        ('Siel_14', 2, 1, 'Normal', NULL, 'Libre', 'S00005'),
                        ('Siel_15', 2, 2, 'Normal', NULL, 'Libre', 'S00005'),
                        ('Siel_16', 2, 3, 'VIP', NULL, 'Libre', 'S00006'),
                        ('Siel_17', 2, 4, 'VIP', NULL, 'Occupé', 'S00006'),
                        ('Siel_18', 3, 1, 'Normal', NULL, 'Libre', 'S00002'),
                        ('Siel_19', 3, 2, 'Normal', NULL, 'Libre', 'S00002'),
                        ('Siel_20', 4, 1, 'Normal', NULL, 'Libre', 'S00002'),
                        ('Siel_21', 7, 2, 'Normal', NULL, 'Libre', 'S00002'),
                        ('Siel_22', 5, 3, 'VIP', NULL, 'Libre', 'S00002'),
                        ('Siel_38', 3, 1, 'Normal', NULL, 'Libre', 'S00008'),
                        ('Siel_39', 3, 2, 'Normal', NULL, 'Libre', 'S00010'),
                        ('Siel_30', 4, 1, 'Normal', NULL, 'Libre', 'S00010'),
                        ('Siel_31', 7, 2, 'Normal', NULL, 'Libre', 'S00010');

--
-- Data for Name: portique; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO portique VALUES ('Portique1001', 'Access-IS', '2021-01-15', '120.18.1.1', 'Active', 'S00002'),
                        ('Portique1010', 'Sony', '2022-01-20', '192.168.1.100', 'Active', 'S00003'),
                        ('Portique1012', 'Samsung', '2022-03-10', '192.168.1.102', 'Active', 'S00004'),
                        ('Portique1014', 'Sharp', '2022-05-01', '192.168.1.104', 'Active', 'S00005'),
                        ('Portique1016', 'Philips', '2022-07-20', '192.168.1.106', 'Active', 'S00006'),
                        ('Portique1018', 'Canon', '2022-09-10', '192.168.1.108', 'Active', 'S00007'),
                        ('Portique0159', 'Epson', '2020-08-16', '145.98.1.4', 'Inactive', 'S00001'),
                        ('Portique1011', 'LG', '2022-02-15', '192.168.1.101', 'Inactive', 'S00003'),
                        ('Portique1013', 'Panasonic', '2022-04-05', '192.168.1.103', 'Inactive', 'S00004'),
                        ('Portique1015', 'Toshiba', '2022-06-25', '192.168.1.105', 'Inactive', 'S00005'),
                        ('Portique1017', 'Hitachi', '2022-08-15', '192.168.1.107', 'Inactive', 'S00010'),
                        ('Portique1019', 'Nikon', '2022-10-05', '192.168.1.109', 'Inactive', 'S00007');


--
-- Data for Name: personne; Type: TABLE DATA; Schema: public; Owner: gate-flick
--


INSERT INTO personne VALUES ('Cli12345678', 'Beaf', 'Rose', 'rbeaf@gmail.com', 'steakrose', '$2y$10$Kx.uAkdYQYmpHglrAWrs8OOVNahIzWXhTPNk5M3r/A90y3smgO6ni'),
                            ('Cli84384168', 'Tamago', 'William', 'wtamago@gmail.com', 'oeufdur', '$2y$10$KPhCnT.C6jg5bihmfbmk3.liptMxfseFycOMckVrYoMNJUqdI9Pry'),
                            ('Emp57618315', 'Flinguer', 'Vaijme', 'vaifling@hotmail.fr', 'vaijmefli', '$2y$10$XxenP831By/Bj0b.LCcIv.wtVleNY5Q6.4L7newpbY4NKxJaNmMya'),
                            ('Cli00000003', 'Martin', 'Luc', 'lucmartin@mail.com', 'lucm', '$2y$10$evgXzMc6O/kpK/7mES2P9OP0iD3RSIS3PsrT4bqyco.c71IbzZwja'),
                            ('Cli00000004', 'Dubois', 'Marie', 'mariedubois@mail.com', 'maried', '$2y$10$6oTefE6jO1URqFLiRddeJOxHaPttyOAJNjqIm8qXa9tp8m0zBcPUm'),
                            ('Cli00000005', 'Bernard', 'Julien', 'julienbernard@mail.com', 'julienb', '$2y$10$R4yQwctCPYk4DCcNcCdQmOrmIAtlfmIeUrf/58FZcTa9nof.Tu/Ny'),
                            ('Cli00000006', 'Petit', 'Sophie', 'sophiepetit@mail.com', 'sophiep', '$2y$10$vNOT9ynlBKoM.envWX9VSuFxyr1L0btJsSxGyFbMJZAijWFFcO4P.'),
                            ('Cli00000007', 'Leroy', 'Emma', 'emmaleroy@mail.com', 'emmal', '$2y$10$4QgYMfjbWfDfdfXSfFuAlOLEFWYxGiWfR8Zv4Duq6gXFOCY0YZkre'),
                            ('Cli00000008', 'Moreau', 'Pierre', 'pierremoreau@mail.com', 'pierrem', '$2y$10$HLfETGfjQBNn4MyEax0tTegCKfWPmb0EUP4XVFlUVSM5yfaF7MoS2'),
                            ('Cli00000009', 'Lefebvre', 'Alice', 'alicelefebvre@mail.com', 'alicel', '$2y$10$ONSh/JAiOIeaG25WqIdwT.65XY0u9bLCbWeBBViRKW/k5Ym.Totdi'),
                            ('Cli00000010', 'Garcia', 'David', 'davidgarcia@mail.com', 'davidg', '$2y$10$wra/h/68Ln09YZFmQeH2sOtQ5yYWaDDYXhgTJ9zdapHnL7Evrkwi2'),
                            ('Cli00000011', 'Roux', 'Chloé', 'chloeroux@mail.com', 'chloer', '$2y$10$fRBwCJfWKAKY1KXm2WWK6elZjpGzdGKSu7Kw7/BYe2WSwEPJ5xcZ6'),
                            ('Cli00000012', 'Fournier', 'Alexandre', 'alexfournier@mail.com', 'alexf', '$2y$10$batmUFd/bU0w5ADmC9Ah5.k.v81y2Hr98t8dWoioVaLMHycWzI/eK');

--
-- Data for Name: client; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO client VALUES ('Cli12345678', 'Regular', 'FR1234567800', 'FR', '1980-05-21'),
                        ('Cli84384168', 'VIP', 'FR1234567801', 'EN', '1984-08-30'),
                        ('Cli00000003', 'New', 'FR1234567802', 'ES', '1992-11-15'),
                        ('Cli00000004', 'Regular', 'FR1234567803', 'DE', '1975-02-05'),
                        ('Cli00000005', 'VIP', 'FR1234567804', 'IT', '1988-09-10'),
                        ('Cli00000006', 'New', 'FR1234567805', 'FR', '1995-12-20'),
                        ('Cli00000007', 'Regular', 'FR1234567806', 'EN', '1970-07-25'),
                        ('Cli00000008', 'VIP', 'FR1234567807', 'ES', '1982-03-18'),
                        ('Cli00000009', 'New', 'FR1234567808', 'DE', '1965-06-09'),
                        ('Cli00000010', 'Regular', 'FR1234567809', 'IT', '1998-01-01');

--
-- Data for Name: employee; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO employee VALUES ('Emp57618315','Agent accueil','Manager','C1509');





--
-- Data for Name: ticket; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO ticket VALUES ('Com405953455', '2023-09-03', '2023-12-03', 'En attente', NULL, 'Siel_03'),
                        ('Com405953459', '2023-09-10', '2023-12-10', 'En attente', NULL, 'Siel_10'),
                        ('Com405953461', '2023-09-12', '2023-12-12', 'En attente', NULL, 'Siel_12'),
                        ('Com405953463', '2023-09-14', '2023-12-14', 'En attente', 'Réduction', 'Siel_14'),
                        ('Com405953465', '2023-09-16', '2023-12-16', 'En attente', NULL, 'Siel_16'),
                        ('Com405953467', '2023-09-18', '2023-12-18', 'En attente', NULL, 'Siel_18'),
                        ('Com405953458', '2023-09-03', '2023-10-16', 'Utilisé', 'VIP', 'Siel_02'),
                        ('Com405953460', '2023-09-11', '2023-12-11', 'Utilisé', 'VIP', 'Siel_11'),
                        ('Com405953462', '2023-09-13', '2023-12-13', 'Utilisé', NULL, 'Siel_13'),
                        ('Com405953464', '2023-09-15', '2023-12-15', 'Problème', NULL, 'Siel_15'),
                        ('Com405953466', '2023-09-17', '2023-12-17', 'Problème', 'VIP', 'Siel_17'),
                        ('Com405953468', '2023-09-19', '2023-12-19', 'Problème', NULL, 'Siel_19');



--
-- Data for Name: jouer; Type: TABLE DATA; Schema: public; Owner: gate-flick
--

INSERT INTO jouer VALUES ('F049828617', 'A01743268'),
                        ('F049828617', 'A02334895'),
                        ('F984137122', 'A03412118'),
                        ('F301928374', 'A10123456'),
                        ('F301928374', 'A10123457'),
                        ('F302837465', 'A10123458'),
                        ('F302837465', 'A10123459'),
                        ('F303746576', 'A10123460'),
                        ('F303746576', 'A10123461'),
                        ('F304655687', 'A10123462'),
                        ('F304655687', 'A10123463'),
                        ('F305564798', 'A10123464'),
                        ('F305564798', 'A10123465');


INSERT INTO acheter VALUES ('Com405953455', 'Cli12345678', 5, 47),
                        ('Com405953458', 'Cli84384168', 1, 24),
                        ('Com405953459', 'Cli12345678', 2, 20),
                        ('Com405953460', 'Cli84384168', 4, 40),
                        ('Com405953461', 'Cli00000003', 1, 10),
                        ('Com405953462', 'Cli00000004', 3, 30),
                        ('Com405953463', 'Cli00000005', 2, 25),
                        ('Com405953464', 'Cli00000006', 4, 45),
                        ('Com405953466', 'Cli00000008', 3, 35),
                        ('Com405953468', 'Cli00000010', 4, 48);
