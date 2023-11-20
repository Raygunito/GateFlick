import json
import psycopg2
import Logger

logger = Logger.startLogger(__name__)


def startConnection():
    with open("db_login.json", "r") as js_file:
        db_logging = json.load(js_file)
    db_connection = psycopg2.connect(**db_logging)
    return db_connection


def handle_database(message: str, step: int, cursor):
    if (step == 0):
        # On exécutera les requetes en fonction du step
        cursor.execute(f"SELECT * FROM ticket WHERE id_ticket = '{message}'", )
        result = cursor.fetchone()
        print(result)
        # si jamais on a un soucis ex = pas de ligne trouvé alors => return False
        return result
    if (step == 1):
        # On exécutera les requetes en fonction du step
        # cursor.execute("""SELECT * FROM ucs;""")
        # result = cursor.fetchall()
        # print(result)
        # si jamais on a un soucis ex = pas de ligne trouvé alors => return False
        return
    return
