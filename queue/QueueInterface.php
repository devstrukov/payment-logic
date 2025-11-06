<?php
namespace app\queue;

interface QueueInterface
{
    public function push(string $queue, array $data): bool;
    public function pop(string $queue): ?array;
    public function process(string $queue, callable $callback): void;
}