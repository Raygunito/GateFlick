import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.net.Socket;
import java.util.Scanner;

public class Sender implements Runnable {
    private Socket socket;

    public Sender(Socket socket) {
        this.socket = socket;
    }

    @Override
    public void run() {
        try (PrintWriter writer = new PrintWriter(new OutputStreamWriter(socket.getOutputStream(), "UTF-8"), true);
             Scanner inputMsg = new Scanner(System.in)) {
            String msgSent = "";
            while (!socket.isClosed() && !msgSent.equalsIgnoreCase("exit")) {
                msgSent = inputMsg.nextLine();
                writer.println(msgSent);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
