import socket
import re
from typing import Tuple
import Logger
import Database
import Constant
import Utility


# Connexion a la base de donnée, les login se trouve dans un fichier JSON a part
db_connection = Database.startConnection()
cursor = db_connection.cursor()

log = Logger.startLogger("MainServer")


# Réponse qui sera basé sur le schéma applicatif du rapport
def decodeMessage(message: str, step: int, array : list) -> str:
    # J'ai eu droit a la spéciale Java avec les caractères spéciaux cachés
    clean_message = Utility.cleanMessage(message)
    #Pré traitement de l'information, refuser tous les messages qui ne suivent pas le regex
    if re.search(Constant.STEP_PATTERNS[step], clean_message):
        requestResponse = Database.handle_database(clean_message, step, cursor, array)
        if not requestResponse:
            return Constant.STEP_FAILURES[step]
             
        return Constant.STEP_RESPONSES[step]
    else:
        return Constant.STEP_FAILURES[step]


def handle_client(client_socket: socket.socket, client_address: Tuple[str, int]) -> None:
    """
    Handle communication with a client.

    Parameters:
    - client_socket: The socket object for communication with the client.
    - client_address: A tuple containing the IP address and port number of the client.
    """
    client_socket.settimeout(60)
    kickTimeOut = False
    step = 0
    array_string = [None,None]
    try:
        while True:
            data = client_socket.recv(Constant.RECV_BUFFER_SIZE).strip()
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
            # Processus et préparation SQL
            # Communication avec la base donnée très important comme ligne
            response = decodeMessage(request, step, array_string)

            if (response == Constant.STEP_RESPONSES[step]):
                step += 1
            else:
                response = response + "- Resetting to Step 1"
                step = 0
            response = response + "\n"

            try:
                client_socket.send(response.encode('utf-8'))
            except socket.error as e:
                log.error(f"Error sending data to {client_address}: {e}")
                break

            # Si jamais on arrrive a la fin de l'échange
            if (Utility.cleanMessage(response) == Constant.STEP_RESPONSES[3]):
                break
    except socket.timeout:
        log.warning(f"Timeout for {client_address}. Closing the connection.")
        timeout_message = "TIMEOUT Connection closed due to timeout."
        try:
            client_socket.send(timeout_message.encode('utf-8'))
        except socket.error as e:
            log.error(f"Error sending timeout message to {client_address}: {e}")

        kickTimeOut = True

        if ((array_string[0]!=None) and (array_string[1]!=None)):
            print("Je vais changer le ticket car Timeout")
            decodeMessage('',step,array_string)
            
    except (socket.error, ConnectionResetError) as e:
        log.error(f"Socket error for {client_address}: {e}")
    finally:
        if kickTimeOut:
            log.info(f"{client_address} has disconnected due to timeout.")
        else:
            log.info(f"{client_address} has disconnected.")
        try:
            client_socket.close()
        except socket.error as e:
            log.error(f"Error closing socket for {client_address}: {e}")


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
