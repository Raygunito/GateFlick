import java.net.Socket;
import java.net.SocketException;
import java.io.IOException;
import java.net.SocketTimeoutException;
import java.util.Scanner;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import org.apache.log4j.Logger;
import log.LoggerUtility;
import java.util.regex.Pattern;

public class SingleThreadClient {
    private static final int SOCKET_TIMEOUT = 30 * 1000; // 30 seconds
    private Socket socket;
    private static Logger logger = LoggerUtility.getLogger(SingleThreadClient.class, "text");
    private int step = 0;

    // Array of regex patterns
    private static final Pattern COM_PATTERN = Pattern.compile("^Com\\d{9}$");
    private static final Pattern PORTIQUE_PATTERN = Pattern.compile("^Portique\\d{4}$");
    private static final Pattern START_UPDATE_PATTERN = Pattern.compile("START UPDATE");
    private static final Pattern RECEIVED_PATTERN = Pattern.compile("RECEIVED");
    public static final Pattern[] STEP_PATTERNS = { COM_PATTERN, PORTIQUE_PATTERN, START_UPDATE_PATTERN,
            RECEIVED_PATTERN };
    private static final Pattern VALID_PATTERN = Pattern.compile("^VALID\\s.*$");
    private static final Pattern REJECT_PATTERN = Pattern.compile("^REJECT\\s.*$");
    private static final Pattern WARN_PATTERN = Pattern.compile("^WARN\\s.*$");
    private static final String[] OUTPUT_CLIENT = { "QR Code non reconnu", "Portique inconnu", "QR Code déjà utilisé ou mauvaise salle",
            "Timeout, veuillez voir l'accueil" };

    public void start() {
        try {
            socket = ConnectionManager.login();
            configureSocket();

            communicateWithServer();

        } catch (IOException e) {
            handleIOException(e);
        } finally {
            closeConnection();
        }
    }

    private void configureSocket() throws IOException {
        try {
            socket.setSoTimeout(SOCKET_TIMEOUT);
        } catch (SocketException se) {
            handleSocketException(se);
        }
    }

    private void communicateWithServer() {
        try (BufferedReader reader = new BufferedReader(new InputStreamReader(socket.getInputStream()));
                PrintWriter writer = new PrintWriter(new OutputStreamWriter(socket.getOutputStream(), "UTF-8"), true);
                Scanner inputMsg = new Scanner(System.in)) {
            String msgSent = "";
            String response;

            while (!socket.isClosed()) {
                try {
                    if (step < 2) {
                        while (!STEP_PATTERNS[step].matcher(msgSent).matches()) {
                            msgSent = inputMsg.nextLine();
                            if (!STEP_PATTERNS[step].matcher(msgSent).matches()) {
                                logger.warn("Code not recognized by protocol tracing : " + msgSent);
                            }
                        }
                    } else {
                        msgSent = STEP_PATTERNS[step].pattern();
                    }
                    sendMessage(msgSent, writer);
                    if (socket.isClosed()) {
                        break;
                    }

                    response = receiveMessage(reader);
                    System.out.println(response);
                    logger.debug("Server response: " + response);
                    if (VALID_PATTERN.matcher(response).matches()) {
                        step++;
                    } else {
                        if (WARN_PATTERN.matcher(response).matches()){
                            if (step == 1) {
                                System.out.println("Attention vous êtes devant la mauvaise salle.");
                                logger.warn("Client Ticket and Portique input are not related");
                            }
                            if (step == 2){
                                System.out.println(("Attention le ticket est déjà utilisé."));
                                logger.warn("Ticket already used");
                            }
                            break;
                        }
                        if (REJECT_PATTERN.matcher(response).matches()) {
                            System.out.println(OUTPUT_CLIENT[step] + "- Restarting to the beginning");
                            step = 0;
                        }
                    }
                    if (step == 4) {
                        closeConnection();
                    }
                    if (msgSent.equalsIgnoreCase("exit") || socket.isClosed()) {
                        break;
                    }
                } catch (SocketTimeoutException ste) {
                    handleSocketTimeoutException(ste, socket);
                    break;
                } catch (IOException e) {
                    handleIOException(e, socket);
                    break;
                }
            }
        } catch (Exception e) {
            logger.warn(e);
        }
    }

    private void sendMessage(String message, PrintWriter writer) {
        writer.println(message);
        logger.debug("Client sent: " + message);
    }

    private String receiveMessage(BufferedReader reader) throws IOException {
        return reader.readLine();
    }

    private void closeConnection() {
        try {
            socket.close();
        } catch (IOException e) {
            handleIOException(e);
        }
    }

    private void handleIOException(IOException e) {
        logger.error("IOException occurred: ", e);
    }

    private void handleIOException(IOException e, Socket socket) {
        logger.error("IOException occurred: ", e);
    }

    private void handleSocketTimeoutException(SocketTimeoutException ste, Socket socket) {
        logger.error("Socket operation timed out: " + socket.getInetAddress());
    }

    private void handleSocketException(SocketException se) {
        logger.error("Socket exception: ", se);
    }

    public static void main(String[] args) {
        new SingleThreadClient().start();
    }
}
