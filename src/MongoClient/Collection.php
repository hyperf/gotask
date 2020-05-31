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

    public function insertOne($document = [], array $opts = []): array
    {
        $document = $this->sanitize($document);
        return $this->mongo->insertOne($this->makePayload([
            'Record' => $document,
        ], $opts));
    }

    public function insertMany($documents = [], array $opts = []): array
    {
        $documents = $this->sanitize($documents);
        return $this->mongo->insertMany($this->makePayload([
            'Records' => $documents,
        ], $opts));
    }

    public function find($filter = [], array $opts = []): ?array
    {
        $filter = $this->sanitize($filter);
        return $this->mongo->find($this->makePayload([
            'Filter' => $filter,
        ], $opts));
    }

    public function findOne($filter = [], array $opts = []): array
    {
        $filter = $this->sanitize($filter);
        return $this->mongo->findOne($this->makePayload([
            'Filter' => $filter,
        ], $opts));
    }

    public function updateOne($filter = [], $update = [], array $opts = []): array
    {
        $filter = $this->sanitize($filter);
        $update = $this->sanitize($update);
        return $this->mongo->updateOne($this->makePayload([
            'Filter' => $filter,
            'Update' => $update,
        ], $opts));
    }

    public function updateMany($filter = [], $update = [], array $opts = []): array
    {
        $filter = $this->sanitize($filter);
        $update = $this->sanitize($update);
        return $this->mongo->updateMany($this->makePayload([
            'Filter' => $filter,
            'Update' => $update,
        ], $opts));
    }

    public function replaceOne($filter = [], $replace = [], array $opts = []): array
    {
        $filter = $this->sanitize($filter);
        $replace = $this->sanitize($replace);
        return $this->mongo->replaceOne($this->makePayload([
            'Filter' => $filter,
            'Replace' => $replace,
        ], $opts));
    }

    public function countDocuments($filter = [], array $opts = []): int
    {
        $filter = $this->sanitize($filter);
        return $this->mongo->countDocuments($this->makePayload([
            'Filter' => $filter,
        ], $opts));
    }

    public function deleteOne($filter = [], array $opts = []): array
    {
        $filter = $this->sanitize($filter);
        return $this->mongo->deleteOne($this->makePayload([
            'Filter' => $filter,
        ], $opts));
    }

    public function deleteMany($filter = [], array $opts = []): array
    {
        $filter = $this->sanitize($filter);
        return $this->mongo->deleteMany($this->makePayload([
            'Filter' => $filter,
        ], $opts));
    }

    public function aggregate($pipeline = [], array $opts = []): ?array
    {
        $pipeline = $this->sanitize($pipeline);
        return $this->mongo->aggregate($this->makePayload([
            'Pipeline' => $pipeline,
        ], $opts));
    }

    public function drop()
    {
        return $this->mongo->drop([
            'Database' => $this->database,
            'Collection' => $this->collection,
        ]);
    }

    private function makePayload(array $partial, array $opts): array
    {
        return array_merge($partial, [
            'Database' => $this->database,
            'Collection' => $this->collection,
            'Opts' => $this->sanitizeOpts($opts),
        ]);
    }
}
