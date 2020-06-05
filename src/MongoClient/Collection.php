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

class Collection
{
    use MongoTrait;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $collection;

    /**
     * @var MongoProxy
     */
    private $mongo;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(MongoProxy $mongo, ConfigInterface $config, string $database, string $collection)
    {
        $this->mongo = $mongo;
        $this->config = $config;
        $this->database = $database;
        $this->collection = $collection;
    }

    public function insertOne($document = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $document = $this->sanitize($document);
        $data = $this->mongo->insertOne($this->makePayload([
            'Record' => $document,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function insertMany($documents = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $documents = $this->sanitize($documents);
        $data = $this->mongo->insertMany($this->makePayload([
            'Records' => $documents,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function find($filter = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->find($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function findOne($filter = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->findOne($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function updateOne($filter = [], $update = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $filter = $this->sanitize($filter);
        $update = $this->sanitize($update);
        $data = $this->mongo->updateOne($this->makePayload([
            'Filter' => $filter,
            'Update' => $update,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function updateMany($filter = [], $update = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $filter = $this->sanitize($filter);
        $update = $this->sanitize($update);
        $data = $this->mongo->updateMany($this->makePayload([
            'Filter' => $filter,
            'Update' => $update,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function replaceOne($filter = [], $replace = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $filter = $this->sanitize($filter);
        $replace = $this->sanitize($replace);
        $data = $this->mongo->replaceOne($this->makePayload([
            'Filter' => $filter,
            'Replace' => $replace,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function countDocuments($filter = [], array $opts = [], $typeMap = ['document' => 'int'])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->countDocuments($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return unpack('P', $data)[1];
    }

    public function deleteOne($filter = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->deleteOne($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function deleteMany($filter = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->deleteMany($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function aggregate($pipeline = [], array $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $pipeline = $this->sanitize($pipeline);
        $data = $this->mongo->aggregate($this->makePayload([
            'Pipeline' => $pipeline,
        ], $opts));
        return toPHP($data, $typeMap);
    }

    public function drop()
    {
        return $this->mongo->drop(fromPHP([
            'Database' => $this->database,
            'Collection' => $this->collection,
        ]));
    }

    private function makePayload(array $partial, array $opts): string
    {
        return fromPHP(array_merge($partial, [
            'Database' => $this->database,
            'Collection' => $this->collection,
            'Opts' => $this->sanitizeOpts($opts),
        ]));
    }
}
