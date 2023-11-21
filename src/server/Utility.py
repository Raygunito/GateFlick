import socket
import Constant
import re
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
        port_str = input("Renseignez le port du serveur [1234] : ")
        if not port_str:
            return Constant.DEFAULT_PORT
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
        except Exception as e:
            print(f"Erreur inattendue : {e}")


def get_valid_host_input():
    while True:
        host_str = input("Renseignez l'adresse IP ou le nom d'hôte du serveur [localhost] : ")
        if not host_str:
            return Constant.DEFAULT_HOST
        try:
            socket.inet_pton(socket.AF_INET, host_str)
            return host_str
        except (socket.error, ValueError):
            try:
                socket.gethostbyname(host_str)
                return host_str
            except (socket.error, socket.herror):
                print("Entrée invalide. Veuillez saisir une adresse IP valide ou un nom d'hôte ou 'localhost'.")


def cleanMessage(message: str) -> str:
    return re.sub(r'[\n\r]', '', message)