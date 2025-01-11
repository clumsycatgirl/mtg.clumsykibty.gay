<?php

namespace App\Controllers\WebSockets;

use App\Models\DataClasses\Tokens;
use App\Models\TokensModel;
use App\Types\WebSocketCardData;
use Lib\Systems\Controllers\WebSocketControllerInterface;
use Ratchet\ConnectionInterface;

class WebSocketController implements WebSocketControllerInterface {
    protected \SplObjectStorage $clients;
    /** @var array<WebSocketCardData> */
    protected array $cards;
    protected int $counter;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->counter = 0;
    }

    public function onOpen(ConnectionInterface $conn): void {
        $this->clients->attach($conn);
        echo "[{$conn?->resourceId}] connected\n";
    }

    public function onMessage(ConnectionInterface $from, mixed $msg): void {
        $num_receivers = count($this->clients) - 1;

        $msg_data = json_decode($msg, false);
        log_info(sprintf('[%d] received \'%s\' data=%s', $from->resourceId, $msg_data->reason, var_export($msg_data, true)));

        if ($msg_data->reason === 'get-counter') {
            $token_uuid = '7c4596d5-af5c-5e16-802e-38e3cee1fdbc';
            $this->cards[$this->counter] =
                $msg->data !== null
                ? new WebSocketCardData($msg_data->data)
                : (new TokensModel())->first_where([
                    'uuid' => $token_uuid
                ]);

            $from->send(json_encode([
                'reason' => 'get-counter',
                'data' => [
                    'counter' => $this->counter,
                ],
                'card' => $this->cards[$this->counter]->to_array(),
            ]));

            $this->counter++;
            return;
        }

        if ($msg_data->reason === 'move' && $this->cards[$msg_data->data->id] !== null) {
            $msg_data->card = $this->cards[$msg_data->data->id]->to_array();
            $msg = json_encode($msg_data);
            // $from->send($msg);
        }

        if ($msg_data->reason === 'chat') {
            $from->send($msg);
        }

        // echo sprintf('[%d] sending \'%s\' to %d other connection%s' . "\n",
        // $from?->resourceId, $msg, $num_receivers, $num_receivers == 1 ? '' : 's');
        foreach ($this->clients as $client) {
            if ($from === $client)
                continue;
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn): void {
        $this->clients->detach($conn);
        echo "[{$conn?->resourceId}] disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void {
        echo "[{$conn?->resourceId}] error '{$e->getMessage()}'\n";
        $conn->close();
    }
}
