import java.net.Socket;
import java.io.IOException;

public class Client {
    private Socket socket;

    public void start() {
        socket = ConnectionManager.login();
        Thread senderThread = new Thread(new Sender(socket));
        Thread receiverThread = new Thread(new Receiver(socket));

        senderThread.start();
        receiverThread.start();

        try {
            senderThread.join();
            receiverThread.join();
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

        closeConnection();
    }

    private void closeConnection() {
        try {
            socket.close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}

