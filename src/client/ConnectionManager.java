import java.util.Scanner;
import java.net.Socket;

public class ConnectionManager {
    public static Socket login() {
        String host = "";
        int port = -1;
        Socket socket = null;
        Scanner input = new Scanner(System.in);
        while (socket == null) {
            System.out.println("IP address to connect to :");
            host = input.nextLine();
            System.out.println("Port to listen :");
            port = input.nextInt();
            input.nextLine();
            try {
                socket = new Socket(host, port);
            } catch (Exception e) {
                System.err.println("Connection failed");
            }
        }
        System.out.println("Successfully connected to IP address : " + host + " on port : " + port);
        return socket;
    }
}

