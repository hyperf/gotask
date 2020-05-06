<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf/GoTask.
 *
 * @link     https://www.github.com/hyperf/gotask
 * @document  https://www.github.com/hyperf/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\GoTask\IPC;

use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\GoTask\GoTask;
use Hyperf\GoTask\Relay\ConnectionRelay;
use Hyperf\GoTask\Wrapper\ByteWrapper;
use Hyperf\Utils\ApplicationContext;
use Spiral\Goridge\Exceptions\PrefixException;
use Spiral\Goridge\Exceptions\ServiceException;
use Spiral\Goridge\Exceptions\TransportException;
use Spiral\Goridge\RelayInterface as Relay;
use Swoole\Coroutine\Server\Connection;

class SocketIPCReceiver
{
    /**
     * @var string
     */
    private $address;

    /**
     * @var \Swoole\Coroutine\Server
     */
    private $server;

    /**
     * @var int
     */
    private $port;

    /**
     * @var bool
     */
    private $quit;

    public function __construct(string $address = '127.0.0.1:6001')
    {
        $split = explode(':', $address, 2);
        if (count($split) === 1) {
            $this->address = 'unix:' . $address;
            $this->port = 0;
        } else {
            $this->address = $split[0];
            $this->port = (int) ($split[1]);
        }
    }

    public function start(): bool
    {
        if ($this->isStarted()) {
            return true;
        }
        $this->server = new \Swoole\Coroutine\Server($this->address, $this->port, false, true);
        $this->quit = false;
        $this->server->handle(function (Connection $conn) {
            $relay = new ConnectionRelay($conn);
            while ($this->quit !== true) {
                try {
                    $body = $relay->receiveSync($headerFlags);
                } catch (PrefixException $e) {
                    $relay->close();
                    break;
                }
                if (! ($headerFlags & Relay::PAYLOAD_CONTROL)) {
                    throw new TransportException('rpc response header is missing');
                }

                $seq = unpack('P', substr($body, -8));
                $method = substr($body, 0, -8);
                // wait for the response
                $body = $relay->receiveSync($bodyFlags);
                $payload = $this->handleBody($body, $bodyFlags);
                try {
                    $response = $this->dispatch($method, $payload);
                    $error = null;
                } catch (\Throwable $e) {
                    $response = null;
                    $error = $e;
                }
                $relay->send(
                    $method . pack('P', $seq[1]),
                    Relay::PAYLOAD_CONTROL | Relay::PAYLOAD_RAW
                );

                if ($error !== null) {
                    $error = $this->formatError($error);
                    $relay->send($error, Relay::PAYLOAD_ERROR | Relay::PAYLOAD_RAW);
                    continue;
                }
                if ($response instanceof ByteWrapper) {
                    $relay->send($response->byte, Relay::PAYLOAD_RAW);
                    continue;
                }
                if (is_null($response)) {
                    $relay->send($response, Relay::PAYLOAD_NONE);
                    continue;
                }
                $relay->send(json_encode($response), 0);
            }
        });
        $this->server->start();
        return true;
    }

    public function close()
    {
        if ($this->server !== null) {
            $this->quit = true;
            $this->server->shutdown();
        }
        $this->server = null;
    }

    protected function dispatch($method, $payload)
    {
        [$class, $handler] = explode('::', $method);
        if (ApplicationContext::hasContainer()) {
            $container = ApplicationContext::getContainer();
            $instance = $container->get($class);
        } else {
            $instance = new $class();
        }
        return $instance->{$handler}($payload);
    }

    protected function isStarted()
    {
        return $this->server !== null;
    }

    /**
     * Handle response body.
     *
     * @param string $body
     *
     * @throws ServiceException
     * @return mixed
     */
    protected function handleBody($body, int $flags)
    {
        if ($flags & GoTask::PAYLOAD_ERROR && $flags & GoTask::PAYLOAD_RAW) {
            throw new ServiceException("error '{$body}' on '{$this->server}'");
        }

        if ($flags & GoTask::PAYLOAD_RAW) {
            return $body;
        }

        return json_decode($body, true);
    }

    private function formatError(\Throwable $error)
    {
        $simpleFormat = $error->getMessage() . ':' . $error->getTraceAsString();
        if (! ApplicationContext::hasContainer()) {
            return $simpleFormat;
        }
        $container = ApplicationContext::getContainer();
        if (! $container->has(FormatterInterface::class)) {
            return $simpleFormat;
        }
        return $container->get(FormatterInterface::class)->format($error);
    }
}
