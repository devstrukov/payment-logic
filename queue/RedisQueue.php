<?php
namespace app\queue;

use yii\redis\Connection;

class RedisQueue implements QueueInterface
{
    private $redis;

    public function __construct(Connection $redis)
    {
        $this->redis = $redis;
    }

    public function push(string $queue, array $data): bool
    {
        return (bool) $this->redis->lpush("queue:{$queue}", json_encode($data));
    }

    public function pop(string $queue): ?array
    {
        $data = $this->redis->rpop("queue:{$queue}");
        return $data ? json_decode($data, true) : null;
    }

    public function process(string $queue, callable $callback): void
    {
        while ($data = $this->pop($queue)) {
            try {
                call_user_func($callback, $data);
            } catch (\Exception $e) {
                \Yii::error("Queue processing error: " . $e->getMessage());
            }
        }
    }
}