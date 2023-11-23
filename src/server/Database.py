import json
import psycopg2
import Logger

logger = Logger.startLogger(__name__)


def startConnection():
    with open("db_login.json", "r") as js_file:
        db_logging = json.load(js_file)
    db_connection = psycopg2.connect(**db_logging)
    return db_connection


def handle_database(message: str, step: int, cursor, string_tab:list):
    try:
        if step == 0:
            cursor.execute(f"SELECT id_ticket FROM ticket WHERE id_ticket = '{message}'")
            result = cursor.fetchone()
            print(result)
            string_tab.insert(0, result[0] if result is not None else None)
            return result
        if step == 1:
            cursor.execute(f"SELECT id_portique FROM portique WHERE id_portique = '{message}'")
            result = cursor.fetchone()
            print(result)
            string_tab.insert(1, result[0] if result is not None else None)
            return result
        if step == 2:
            cursor.execute(f"UPDATE Ticket SET statut_usage = 'Used' WHERE id_ticket = '{message}'")
            # Ne pas oublier de .commit() !!
            return True
        if step == 3:
            cursor.execute(f"UPDATE Ticket SET statut_usage = 'Problem' WHERE id_ticket = '{message}'")
            # Ne pas oublier de .commit() !!
            return True
        
        print("Invalid step value")
        return None
    except Exception as e:
        print(f"Error executing query for step {step}: {e}")
        return False  
