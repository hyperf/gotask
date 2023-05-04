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
namespace Hyperf\GoTask\MongoClient;

use Hyperf\Contract\ConfigInterface;

use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toPHP;

class Database
{
    use MongoTrait;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var MongoProxy
     */
    private $mongo;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var array
     */
    private $typeMap;

    public function __construct(MongoProxy $mongo, ConfigInterface $config, string $database, array $typeMap)
    {
        $this->mongo = $mongo;
        $this->config = $config;
        $this->database = $database;
        $this->typeMap = $typeMap;
    }

    public function __get($collName)
    {
        return new Collection($this->mongo, $this->config, $this->database, $collName, $this->typeMap);
    }

    public function collection($collName)
    {
        return new Collection($this->mongo, $this->config, $this->database, $collName, $this->typeMap);
    }

    public function runCommand($command = [], $opts = [])
    {
        $payload = [
            'Database' => $this->database,
            'Command' => $this->sanitize($command),
            'Opts' => $this->sanitizeOpts($opts),
        ];
        $result = $this->mongo->runCommand(fromPHP($payload));
        if ($result !== '') {
            $typeMap = $opts['typeMap'] ?? $this->typeMap;
            return toPHP($result, $typeMap);
        }
        return '';
    }

    public function runCommandCursor($command = [], $opts = [])
    {
        $payload = [
            'Database' => $this->database,
            'Command' => $this->sanitize($command),
            'Opts' => $this->sanitizeOpts($opts),
        ];
        $result = $this->mongo->runCommandCursor(fromPHP($payload));
        if ($result !== '') {
            $typeMap = $opts['typeMap'] ?? $this->typeMap;
            return toPHP($result, $typeMap);
        }
        return '';
    }
}
