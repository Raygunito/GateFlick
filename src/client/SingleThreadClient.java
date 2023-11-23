import java.net.Socket;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.util.Scanner;

import org.apache.log4j.Logger;

import log.LoggerUtility;

public class SingleThreadClient {
    private Socket socket;
    private static Logger logger = LoggerUtility.getLogger(SingleThreadClient.class, "text");

    public void start() {
        socket = ConnectionManager.login();

        try (
            BufferedReader reader = new BufferedReader(new InputStreamReader(socket.getInputStream()));
            PrintWriter writer = new PrintWriter(new OutputStreamWriter(socket.getOutputStream(), "UTF-8"), true);
            Scanner inputMsg = new Scanner(System.in)
        ) {
            String msgSent = "";
            String response;

            System.out.print("Enter the first message: ");
            msgSent = inputMsg.nextLine();
            writer.println(msgSent);

            while (!socket.isClosed()) {
                if (reader.ready()) {
                    response = reader.readLine();
                    System.out.println("Server response: " + response);

                    if (response.equalsIgnoreCase("exit")) {
                        break;
                    }

                    System.out.print("Enter the next message: ");
                    msgSent = inputMsg.nextLine();
                    writer.println(msgSent);

                    if (msgSent.equalsIgnoreCase("exit")) {
                        break;
                    }
                }
            }

        } catch (IOException e) {
            e.printStackTrace();
        } finally {
            closeConnection();
        }
    }

    private void closeConnection() {
        try {
            socket.close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        new SingleThreadClient().start();
    }
}
