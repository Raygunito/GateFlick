import json
import psycopg2
import Logger
import Constant

logger = Logger.startLogger(__name__)


def startConnection():
    with open("db_login.json", "r") as js_file:
        db_logging = json.load(js_file)
    db_connection = psycopg2.connect(**db_logging)
    return db_connection


def handle_database(message: str, step: int, cursor, string_tab:list, dbc):
    try:
        if step == 0:
            cursor.execute(f"SELECT id_ticket FROM ticket WHERE id_ticket = '{message}'")
            result = cursor.fetchone()
            dbc.commit()
            string_tab.insert(0, result[0] if result is not None else None)
            return result
        if step == 1:

            tmp = f"SELECT p.id_portique,t.id_ticket FROM portique p JOIN salle s ON p.id_salle = s.id_salle JOIN siege si ON si.id_salle = s.id_salle JOIN Ticket t ON t.id_siege = si.id_siege WHERE p.id_portique = '{message}' AND t.id_ticket = '{string_tab[0]}'";
            cursor.execute(tmp);
            result = cursor.fetchone()
            dbc.commit()
            if not result:
                return False
            cursor.execute(f"SELECT id_portique FROM portique WHERE id_portique = '{message}'")
            result = cursor.fetchone()
            dbc.commit()
            string_tab.insert(1, result[0] if result is not None else None)
            return result
        if step == 2:
            # Attention on doit vérifier que le ticket na pas utilisé !!!
            tmp = f"SELECT * FROM Ticket t WHERE t.id_ticket = '{string_tab[0]}' AND t.statut_usage != 'Utilisé'";
            cursor.execute(tmp);
            result = cursor.fetchone()
            dbc.commit()
            if not result:
                return False
            cursor.execute(f"UPDATE Ticket SET statut_usage = 'Utilisé' WHERE id_ticket = '{string_tab[0]}'")
            dbc.commit()
            # Ne pas oublier de .commit() !!
            return True
        if step == 3:
            return True
        if step == 4:
            cursor.execute(f"UPDATE Ticket SET statut_usage = 'Problème' WHERE id_ticket = '{string_tab[0]}'")
            dbc.commit()
            # Ne pas oublier de .commit() !!
            return True
        
        print("Invalid step value")
        return None
    except Exception as e:
        print(f"Error executing query for step {step}: {e}")
        return False  
