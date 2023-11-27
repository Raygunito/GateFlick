-- Sélection des informations sur les séances d'un film particulier qui ne sont pas en cours.

SELECT
    c.nom_cinema,
    s.nom_salle,
    se.id_seance,
    se.heure_projection,
    se.langue,
    se.statut,
    se.prix
FROM cinema c
    JOIN salle s ON c.id_cinema = s.id_cinema
    JOIN seance se ON s.id_salle = se.id_salle
WHERE
    se.id_film = se.id_film
    AND se.statut != 'En cours';

-- Sélection de tous les billets en joignant naturellement les tables Siege, Salle et Cinema, filtrant les résultats pour inclure uniquement les billets avec un statut d'utilisation en 'Problème'.

SELECT *
FROM Ticket
    NATURAL JOIN Siege
    NATURAL JOIN Salle
    NATURAL JOIN Cinema
WHERE statut_usage = 'Problème';

-- Sélection des identifiants des sièges disponibles (statut = 'Libre') pour une séance spécifique.

SELECT id_siege
FROM siege
WHERE id_salle IN (
        SELECT id_salle
        FROM seance
        WHERE
            id_seance = id_seance
    )
    AND statut = 'Libre';

-- Sélection des détails des billets achetés par un client spécifique, incluant l'identifiant du billet, le titre du film, le nom du cinéma, le nom de la salle, l'heure de projection, le numéro du siège et le statut d'utilisation du billet.

SELECT
    t.id_ticket,
    f.titre,
    c.nom_cinema,
    sa.nom_salle,
    se.heure_projection,
    s.numero_siege,
    t.statut_usage
FROM acheter a
    JOIN ticket t ON a.id_ticket = t.id_ticket
    JOIN siege s ON t.id_siege = s.id_siege
    JOIN salle sa ON s.id_salle = sa.id_salle
    JOIN cinema c ON sa.id_cinema = c.id_cinema
    JOIN seance se ON sa.id_salle = se.id_salle
    JOIN film f ON se.id_film = f.id_film
WHERE a.id_client = a.id_client;

-- Sélection du statut d'utilisation et de la date d'expiration d'un ticket spécifique.

SELECT
    statut_usage,
    date_expiration
FROM ticket
WHERE id_ticket = id_ticket;

--Permet de récupérer les séances en cours dans tous les cinémas (Entre maintenant et 2 jours pour faciliter nos chances de trouver un film en cours)

SELECT c.nom_cinema, f.titre
FROM seance se
    JOIN film f ON se.id_film = f.id_film
    JOIN salle sal ON se.id_salle = sal.id_salle
    JOIN cinema c ON sal.id_cinema = c.id_cinema
WHERE
    se.heure_projection <= CURRENT_TIMESTAMP
    AND CURRENT_TIMESTAMP <= se.heure_projection + INTERVAL '2 day'
GROUP BY
    c.nom_cinema,
    f.titre;

--Permet de récupérer le chiffre d'affaire total de chaque cinémas et classé par ordre décroisant

SELECT
    c.nom_cinema,
    SUM(a.prix_total) AS Total_Revenue
FROM acheter a
    JOIN ticket t ON a.id_ticket = t.id_ticket
    JOIN siege s ON t.id_siege = s.id_siege
    JOIN salle sal ON s.id_salle = sal.id_salle
    JOIN cinema c ON sal.id_cinema = c.id_cinema
GROUP BY c.nom_cinema
ORDER BY Total_Revenue DESC;

--Permet de récupérer les clients les plus fidèles (ceux qui ont dépensé le plus) et trie par ordre décroissant sur notre site nous récupérons seulement le top 5

SELECT
    p.*,
    c.*,
    SUM(a.prix_total) AS total_depense
FROM Client c
    JOIN Personne p ON c.ID_Client = p.ID_personne
    JOIN Acheter a ON c.ID_Client = a.ID_Client
GROUP BY
    c.ID_Client,
    p.id_personne
ORDER BY total_depense DESC;

-- Permet de récupérer les tickets dont le statut est "Problème", c'est seulement du côté Réseau que ce statut peut survenir

SELECT *
FROM Ticket
    NATURAL JOIN Siege
    NATURAL JOIN Salle
    NATURAL JOIN Cinema
WHERE statut_usage = 'Problème';

--Permet de savoir pour un client donné, combien d'argent il a dépensé au total sur notre site internet

SELECT
    SUM(a.prix_total) AS Total_Spent
FROM acheter a
    JOIN ticket t ON a.id_ticket = t.id_ticket
    JOIN siege s ON t.id_siege = s.id_siege
    JOIN salle sal ON s.id_salle = sal.id_salle
    JOIN cinema c ON sal.id_cinema = c.id_cinema
WHERE a.id_client = id_client;