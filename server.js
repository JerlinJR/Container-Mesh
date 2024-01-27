const fs = require('fs');
const WebSocket = require('ws');

const ERROR_LOG_PATH = '/var/www/wireguard-rest-api/log.txt';
const HOST = '94.237.67.78';
const PORT = 8080;

const sendErrorLogs = (client) => {
  try {
    const logs = fs.readFileSync(ERROR_LOG_PATH, 'utf-8');

    // Split logs into an array of lines
    const logLines = logs.split('\n');

    // Add timestamps to each log entry
    const logsWithTimestamp = logLines.map((log) => {
      const timestamp = new Date().toISOString(); // Use ISO format for timestamp
      // const logWithTimestamp = `${timestamp} - ${log}`;
      const logWithTimestamp = `${log}`;
      console.log(logWithTimestamp); // Log timestamp to console
      return logWithTimestamp;
    });

    // Send the error logs to the connected client
    client.send(logsWithTimestamp.join('\n'));

    // Sleep for a short interval (adjust as needed)
    setTimeout(() => sendErrorLogs(client), 1000);
  } catch (error) {
    console.error(`Error in sendErrorLogs: ${error.message}`);
  }
};

const sendKeepAlive = (client) => {
  try {
    // Send a keep-alive message to the connected client
    client.send('ping');

    // Sleep for a short interval (adjust as needed)
    setTimeout(() => sendKeepAlive(client), 10000);
  } catch (error) {
    console.error(`Error in sendKeepAlive: ${error.message}`);
  }
};

const handleConnection = (ws, req) => {
  console.log(`Accepted connection from ${req.connection.remoteAddress}`);

  // Start a new thread to send error logs to the connected client
  sendErrorLogs(ws);

  // Start a new thread to send keep-alive messages
  sendKeepAlive(ws);

  ws.on('close', () => {
    console.log('Client disconnected');
  });
};

const server = new WebSocket.Server({ noServer: true });

server.on('connection', handleConnection);

const httpServer = require('http').createServer();
httpServer.on('upgrade', (request, socket, head) => {
  server.handleUpgrade(request, socket, head, (ws) => {
    server.emit('connection', ws, request);
  });
});

httpServer.listen(PORT, HOST, () => {
  console.log(`Server listening on http://${HOST}:${PORT}`);
});
