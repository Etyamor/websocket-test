<?php

error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;

class Chat implements MessageComponentInterface {
  protected $clients;

  public function __construct() {
    $this->clients = new \SplObjectStorage;
  }

  public function onOpen(ConnectionInterface $conn) {
    // Store the new connection
    $this->clients->attach($conn);

    $queryParams = [];
    parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
    echo $queryParams['username'];
    echo "New connection! ({$conn->resourceId})\n";
  }

  public function onMessage(ConnectionInterface $from, $msg) {
    echo $msg . "\n";
    foreach ($this->clients as $client) {
      //if ($from !== $client) {
        $client->send($msg);
      //}
    }
  }

  public function onClose(ConnectionInterface $conn) {
    // The connection is closed, remove it
    $this->clients->detach($conn);
    echo "Connection {$conn->resourceId} has disconnected\n";
  }

  public function onError(ConnectionInterface $conn, \Exception $e) {
    echo "An error has occurred: {$e->getMessage()}\n";
    $conn->close();
  }
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Create and run the WebSocket server on port 8080
$app = new App('localhost', 8080, '0.0.0.0');
$app->route('/chat', new Chat, ['*']);
$app->run();
