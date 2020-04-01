<?php


namespace Reasno\GoTask\Wrapper;


use Psr\Log\LoggerInterface;

class LoggerWrapper
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log($payload){
        $this->logger->log($payload['level'], $payload['message'], $payload['context']);
        return null;
    }

}
