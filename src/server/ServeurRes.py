import socket
import re
import Logger
import Database
import Constant
import Utility


# Connexion a la base de donnée, les login se trouve dans un fichier JSON a part
db_connection = Database.startConnection()
cursor = db_connection.cursor()

log = Logger.startLogger("MainServer")


# Réponse qui sera basé sur le schéma applicatif du rapport
def decodeMessage(message: str, step: int) -> str:
    # J'ai eu droit a la spéciale Java avec les caractères spéciaux cachés
    clean_message = Utility.cleanMessage(message)

    if re.search(Constant.STEP_PATTERNS[step], clean_message):
        Database.handle_database(clean_message, step, cursor)
        return Constant.STEP_RESPONSES[step]
    else:
        return Constant.STEP_FAILURES[step]


def handle_client(client_socket, client_address):
    client_socket.settimeout(60)
    kickTimeOut = False
    step = 0
    try:
        while True:
            data = client_socket.recv(1024).strip()
            if (not data):
                break
            if int(len(data)) > Constant.MAX_MESSAGE:
                log.error(f"Message size overflow from {client_address}. Disconnecting client.")
                client_socket.close()
                return
            # Décodage du message
            try:
                request = Utility.cleanMessage(data.decode('utf-8'))
            except UnicodeDecodeError:
                log.error(f"Error decoding data from {client_address}. Disconnecting client.")
                client_socket.close()
                return
            log.debug(f"Received data from client: {Utility.cleanMessage(request)}")
            # Processus de decodage et de préparation SQL
            response = decodeMessage(request, step)

            if (response == Constant.STEP_RESPONSES[step]):
                step += 1
            else:
                response = response + "- Resetting to Step 1"
                step = 0
            response = response + "\n"
            client_socket.send(response.encode('utf-8'))
            # Si jamais on arrrive a la fin de l'échange
            if (Utility.cleanMessage(response) == Constant.STEP_RESPONSES[3]):
                break
    except socket.timeout:
        log.warning(f"Timeout for {client_address}. Closing the connection.")
        timeout_message = "TIMEOUT Connection closed due to timeout."
        client_socket.send(timeout_message.encode('utf-8'))
        kickTimeOut = True

    finally:
        if kickTimeOut:
            log.info(f"{client_address} has disconnected due to timeout.")
        else:
            log.info(f"{client_address} has disconnected.")
        client_socket.close()


success = False
while not success:
    host = Utility.get_valid_host_input()
    port = Utility.get_valid_port_input()

    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

    try:
        server_socket.bind((host, port))
        server_socket.listen(3)
        print(f"Server is listening on {host}:{port}")
        log.info(f"Server is listening on {host}:{port}")
        success = True
    except OSError as e:
        print(f"Error binding to address {host}:{port}: {e}")
        log.warning(f"Error binding to address {host}:{port}: {e}")
        server_socket.close()

while True:
    client_socket, client_address = server_socket.accept()
    log.info(f"Accepted connection from {client_address}")
    handle_client(client_socket, client_address)
    # Pour activer le multiclient on utilisera des threads
    # client_thread = threading.Thread(target=handle_client, args=(client_socket,))
    # client_thread.start()
server_socket.close()
