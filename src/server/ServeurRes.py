import socket
import re
import Logger
import Database

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
db_connection = Database.startConnection()
cursor = db_connection.cursor()

log = Logger.startLogger("MainServer")


def cleanMessage(message: str) -> str:
    return re.sub(r'[\n\r]', '', message)


# Réponse qui sera basé sur le schéma applicatif du rapport
def decodeMessage(message: str, step: int) -> str:
    # J'ai eu droit a la spéciale Java avec les caractères spéciaux cachés
    clean_message = cleanMessage(message)
    if re.search(step_patterns[step], clean_message):
        Database.handle_database(clean_message, step,cursor)
        return step_responses[step]
    else:
        return "REJECT Not found"

def handle_client(client_socket, client_address):
    client_socket.settimeout(60)
    kickTimeOut = False
    step = 0
    while True:
        try:
            data = client_socket.recv(1024)
            if not data:
                break
            request = data.decode('utf-8')
            log.debug(f"Received data from client: {cleanMessage(request)}")
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
        except socket.timeout:
            log.warning(f"Timeout for {client_address}. Closing the connection.")
            timeout_message = "TIMEOUT Connection closed due to timeout."
            client_socket.send(timeout_message.encode('utf-8'))
            kickTimeOut = True
            break
    if kickTimeOut:
        log.info(f"{client_address} has disconnected due to timeout.")
    else:
        log.info(f"{client_address} has disconnected.")
    client_socket.close()

def is_port_open(host, port):
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.settimeout(1)
            s.connect((host, port))
        return False
    except (socket.timeout, ConnectionRefusedError):
        return True

def get_valid_port_input():
    while True:
        port_str = input("Renseignez le port du serveur : ")
        try:
            port = int(port_str)
            if 1 <= port <= 65535:
                if is_port_open("localhost", port):
                    return port
                else:
                    print(f"Le port {port} est déjà utilisé. Veuillez choisir un autre port.")
            else:
                print("Le port doit être compris entre 1 et 65535.")
        except ValueError:
            print("Entrée invalide. Veuillez saisir un nombre entier valide.")

def get_valid_host_input():
    while True:
        host_str = input("Renseignez l'adresse IP du serveur : ")
        try:
            socket.inet_pton(socket.AF_INET, host_str)
            return host_str
        except socket.error:
            if host_str.lower() == "localhost":
                return host_str
            else:
                print("Entrée invalide. Veuillez saisir une adresse IP valide ou 'localhost'.")

# Define the server's host and port
host = get_valid_host_input()
port = get_valid_port_input()

server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

server_socket.bind((host, port))
server_socket.listen(3)

log.info(f"Server is listening on {host}:{port}")
while True:
    client_socket, client_address = server_socket.accept()
    log.info(f"Accepted connection from {client_address}")
    handle_client(client_socket, client_address)
    # Pour activer le multiclient on utilisera des threads
    # client_thread = threading.Thread(target=handle_client, args=(client_socket,))
    # client_thread.start()
server_socket.close()
