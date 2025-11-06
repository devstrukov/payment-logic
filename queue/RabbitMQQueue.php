<?php
namespace app\queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQQueue implements QueueInterface
{
    private $connection;
    private $channel;

    public function __construct(string $host, int $port, string $user, string $password)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
    }

    public function push(string $queue, array $data): bool
    {
        $this->channel->queue_declare($queue, false, true, false, false);

        $msg = new AMQPMessage(
            json_encode($data),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, '', $queue);
        return true;
    }

    public function pop(string $queue): ?array
    {
        // Для RabbitMQ это более сложная логика с callback
        // Пока возвращаем null, реализуем в worker
        return null;
    }

    public function process(string $queue, callable $callback): void
    {
        $this->channel->queue_declare($queue, false, true, false, false);

        $this->channel->basic_consume(
            $queue,
            '',
            false,
            true,
            false,
            false,
            function ($msg) use ($callback) {
                try {
                    $data = json_decode($msg->body, true);
                    call_user_func($callback, $data);
                } catch (\Exception $e) {
                    \Yii::error("Queue processing error: " . $e->getMessage());
                }
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
}