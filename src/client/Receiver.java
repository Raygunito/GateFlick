import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.Socket;

public class Receiver implements Runnable {
    private Socket socket;

    public Receiver(Socket socket) {
        this.socket = socket;
    }

    @Override
    public void run() {
        try (BufferedReader reader = new BufferedReader(new InputStreamReader(socket.getInputStream()))) {
            String response;
            while (!socket.isClosed()) {
                response = reader.readLine();
                if (response != null) {
                    System.out.println("Server response: " + response);
                    if (response.equalsIgnoreCase("exit")) {
                        break;
                    }
                } else {
                    System.out.println("No response from the server.");
                    break;
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
