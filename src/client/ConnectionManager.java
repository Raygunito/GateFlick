import java.net.Socket;
import java.util.Scanner;

import org.apache.log4j.Logger;

import log.LoggerUtility;

public class ConnectionManager {

    public static final String DEFAULT_PORT = "1234";
    public static final String DEFAULT_ADDRESS = "localhost";
    private static Logger logger = LoggerUtility.getLogger(ConnectionManager.class, "text");

    public static Socket login() {
        boolean success = false;
        String host = "";
        String port = "";
        Socket socket = null;
        while (!success) {
            host = getValidHostInput();
            port = getValidPortInput();
            try {
                socket = new Socket(host, Integer.parseInt(port));
                success = true;
            } catch (Exception e) {
                System.err.println("Connection failed");
            }
        }
        System.out.println("Successfully connected to IP address: " + host + " on port: " + port);
        logger.info("Successfully connected to IP address: " + host + " on port: " + port);
        return socket;
    }

    public static String getValidPortInput() {
        Scanner input = new Scanner(System.in);
        while (true) {
            System.out.println("Enter the server port [" + DEFAULT_PORT + "]: ");
            String portStr = input.nextLine();

            if (portStr.isEmpty()) {
                return DEFAULT_PORT;
            }

            try {
                int port = Integer.parseInt(portStr);
                if (port >= 1 && port <= 65535) {
                    return Integer.toString(port);
                } else {
                    System.err.println("Port must be between 1 and 65535.");
                }
            } catch (NumberFormatException e) {
                System.err.println("Invalid port number. Please enter a valid integer.");
            } catch (Exception e) {
                System.err.println("Unexpected error while getting port ");
            }
        }

    }

    public static String getValidHostInput() {
        Scanner input = new Scanner(System.in);
        while (true) {
            System.out.println("Enter the server IP address or hostname [" + DEFAULT_ADDRESS + "]: ");
            String host = input.nextLine();

            if (host.isEmpty()) {
                return DEFAULT_ADDRESS;
            }
            try {
                java.net.InetAddress.getByName(host);
                return host;
            } catch (java.net.UnknownHostException e) {
                System.err.println("Invalid input. Please enter a valid IP address or hostname.");
            } catch (Exception e) {
                System.err.println("Unexpected error while getting hostname ");
            }
        }

    }
}
