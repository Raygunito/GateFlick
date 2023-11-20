import json
import psycopg2
def startConnection():
    with open("db_login.json", "r") as js_file:
        db_logging = json.load(js_file)
    db_connection = psycopg2.connect(**db_logging)
    return db_connection


def handle_database(message: str, step: int, cursor):
    if (step == 0):
        # On exécutera les requetes en fonction du step
        # cursor.execute("""SELECT * FROM ucs;""")
        # result = cursor.fetchall()
        # print(result)
        # si jamais on a un soucis ex = pas de ligne trouvé alors => return False
        return
    return

