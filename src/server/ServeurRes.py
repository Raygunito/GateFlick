import socket
import re
import psycopg2
import json
import threading
step_patterns = [
    r'^Com\d{9}$',        # Step 1: Match "Com" followed by 9 digits
    r'^Portique\d{4}$',  # Step 2: Match "Portique" followed by 4 digits
    r'START UPDATE',
    r'RECEIVED'
]

step_responses = [
    "VALID Ticket found",
    "VALID Serial found",
    "VALID Database updated",
    "VALID Closing socket..."
]
# Connexion a la base de donnée, les login se trouve dans un fichier JSON a part
with open("db_login.json", "r") as js_file:
    db_logging = json.load(js_file)
db_connection = psycopg2.connect(**db_logging)
cursor = db_connection.cursor()


def cleanMessage(message: str) -> str:
    return re.sub(r'[\n\r]', '', message)


# Réponse qui sera basé sur le schéma applicatif du rapport
def decodeMessage(message: str, step: int) -> str:
    # J'ai eu droit a la spéciale Java avec les caractères spéciaux cachés
    clean_message = cleanMessage(message)
    if re.search(step_patterns[step], clean_message):
        handle_database(clean_message, step)
        return step_responses[step]
    else:
        return "REJECT Not found"


def handle_database(message: str, step: int):
    global cursor
    if (step == 1):
        # On exécutera les requetes en fonction du step 
        # cursor.execute("""SELECT * FROM ucs;""")
        # result = cursor.fetchall()
        # print(result) 
        #si jamais on a un soucis ex = pas de ligne trouvé alors => return False
        return
    return


def handle_client(client_socket):
    step = 0
    while True:
        data = client_socket.recv(1024)
        if not data:
            break
        request = data.decode('utf-8')
        print(f"Received data from client: {request}", end="")
        response = decodeMessage(request, step)
        if (response == step_responses[step]):
            step += 1
        else:
            response = "REJECT Invalid message - Resetting to Step 1"
            step = 0
        response = response + "\n"
        client_socket.send(response.encode('utf-8'))
        if (cleanMessage(response) == step_responses[3]):
            break
    print("Client has disconnected.")
    client_socket.close()


# Define the server's host and port
host = input("Renseignez l'adresse IP du serveur : ")
port = int(input("Renseignez le port du serveur : "))

server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

server_socket.bind((host, port))
server_socket.listen(3)

print(f"Server is listening on {host}:{port}")
while True:
    client_socket, client_address = server_socket.accept()
    print(f"Accepted connection from {client_address}")
    handle_client(client_socket)
    # Pour activer le multiclient on utilisera des threads
    # client_thread = threading.Thread(target=handle_client, args=(client_socket,))
    # client_thread.start()
server_socket.close()
