<?php

declare(strict_types=1);

namespace KSamuel\RrService\Connection;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

/**
 * Class RabbitExchange
 * @package App\Connection
 */
class Rabbit implements ConnectionInterface
{
    /**
     * @var AMQPStreamConnection
     */
    protected AMQPStreamConnection $connection;
    /**
     * @var AMQPChannel
     */
    protected AMQPChannel $channel;
    /**
     * @var array<string,mixed>
     */
    protected array $config;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }
    }

    /**
     * Compatibility hack
     * @return ConnectionInterface
     */
    public function connect(): ConnectionInterface
    {
        if (!isset($this->connection)) {
            $connectOptions = $this->config['connection'];
            $this->connection = new AMQPStreamConnection(
                $connectOptions['host'],
                $connectOptions['port'],
                $connectOptions['user'],
                $connectOptions['password']
            );
        } else {
            $this->connection->reconnect();
        }
        $this->channel = $this->connection->channel();

        //declare queues, exchanges and bindings
        $this->setDefinitions($this->config['definitions']);

        if (isset($this->config['qos'])) {
            $this->setQos($this->config['qos']);
        }

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function close(): void
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
        unset($this->channel, $this->connection);
    }

    /**
     * @param string $exchange
     * @param string $routingKey
     * @param string $body
     * @param string $contentType
     * @return bool
     */
    public function insert(
        string $exchange,
        string $routingKey,
        string $body,
        string $contentType = 'text/plain' /* application/json */
    ): bool {
        if (!isset($this->connection)) {
            $this->connect();
        }

        $message = new AMQPMessage($body, [
            'content_type' => $contentType,
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
        $this->channel->basic_publish($message, $exchange, $routingKey);
        return true;
    }

    /**
     * @return null
     */
    public function getProfiler()
    {
        return null;
    }

    /**
     * @param array<string,mixed> $definitions
     * @return void
     */
    private function setDefinitions(array $definitions): void
    {
        foreach ($definitions['exchange_declare'] as $definition) {
            $this->channel->exchange_declare(
                $definition['name'],
                $definition['type'],
                $definition['passive'],
                $definition['durable'],
                $definition['auto_delete']
            );
        }

        foreach ($definitions['queue_declare'] as $definition) {
            $arguments = [];
            if (isset($definition['arguments'])) {
                $arguments = $definition['arguments'];
            }

            $this->channel->queue_declare(
                $definition['name'],
                $definition['passive'],
                $definition['durable'],
                $definition['exclusive'],
                $definition['auto_delete'],
                $definition['nowait'],
                new AMQPTable($arguments)
            );
        }

        foreach ($definitions['queue_bind'] as $name => $definition) {
            if (is_string($definition)) {
                $this->channel->queue_bind(
                    $name,
                    $definition,
                    $name
                );
            }
            if (is_array($definition)) {
                $this->channel->queue_bind(
                    $definition['queue'],
                    $definition['exchange'],
                    $definition['routing_key']
                );
            }
        }
    }

    /**
     * @param array<string,mixed> $qos
     * @return void
     */
    private function setQos(array $qos): void
    {
        $this->channel->basic_qos(
            $qos['prefetch_size'],
            $qos['prefetch_count'],
            $qos['a_global']
        );
    }

    /**
     * @param string $queue - Queue from where to get the messages
     * @param string $consumerTag - Consumer identifier
     * @param bool $noLocal -  Don't receive messages published by this consumer.
     * @param bool $noAck - set to true, automatic acknowledgement mode will be used by this consumer.
     * See https://www.rabbitmq.com/confirms.html for details
     * @param bool $exclusive - Request exclusive consumer access, meaning only this consumer can access the queue
     * @param bool $noWait
     * @param mixed $callback A PHP Callback: string, array, function (\PhpAmqpLib\Message\AMQPMessage $message)
     * @throws \ErrorException
     */
    public function consume(
        string $queue,
        string $consumerTag,
        bool $noLocal,
        bool $noAck,
        bool $exclusive,
        bool $noWait,
        $callback
    ): void {
        if (!isset($this->connection)) {
            $this->connect();
        }

        $this->channel->basic_consume($queue, $consumerTag, $noLocal, $noAck, $exclusive, $noWait, $callback);
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}
