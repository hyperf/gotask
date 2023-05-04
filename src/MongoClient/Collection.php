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
use Hyperf\GoTask\MongoClient\Type\BulkWriteResult;
use Hyperf\GoTask\MongoClient\Type\DeleteResult;
use Hyperf\GoTask\MongoClient\Type\IndexInfo;
use Hyperf\GoTask\MongoClient\Type\InsertManyResult;
use Hyperf\GoTask\MongoClient\Type\InsertOneResult;
use Hyperf\GoTask\MongoClient\Type\UpdateResult;

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

    /**
     * @var array
     */
    private $typeMap;

    public function __construct(MongoProxy $mongo, ConfigInterface $config, string $database, string $collection, array $typeMap)
    {
        $this->mongo = $mongo;
        $this->config = $config;
        $this->database = $database;
        $this->collection = $collection;
        $this->typeMap = $typeMap;
    }

    public function insertOne($document = [], array $opts = []): InsertOneResult
    {
        $document = $this->sanitize($document);
        $data = $this->mongo->insertOne($this->makePayload([
            'Record' => $document,
        ], $opts));
        return toPHP($data, ['root' => InsertOneResult::class]);
    }

    public function insertMany($documents = [], array $opts = []): InsertManyResult
    {
        $documents = $this->sanitize($documents);
        $data = $this->mongo->insertMany($this->makePayload([
            'Records' => $documents,
        ], $opts));
        return toPHP($data, ['root' => InsertManyResult::class]);
    }

    public function find($filter = [], array $opts = [])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->find($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data !== '' ? toPHP($data, $typeMap) : [];
    }

    public function findOne($filter = [], array $opts = [])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->findOne($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data !== '' ? toPHP($data, $typeMap) : [];
    }

    public function findOneAndDelete($filter = [], array $opts = [])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->findOneAndDelete($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data !== '' ? toPHP($data, $typeMap) : [];
    }

    public function findOneAndUpdate($filter = [], $update = [], array $opts = [])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->findOneAndUpdate($this->makePayload([
            'Filter' => $filter,
            'Update' => $update,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data !== '' ? toPHP($data, $typeMap) : [];
    }

    public function findOneAndReplace($filter = [], $replace = [], array $opts = [])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->findOneAndReplace($this->makePayload([
            'Filter' => $filter,
            'Replace' => $replace,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data !== '' ? toPHP($data, $typeMap) : [];
    }

    public function updateOne($filter = [], $update = [], array $opts = []): UpdateResult
    {
        $filter = $this->sanitize($filter);
        $update = $this->sanitize($update);
        $data = $this->mongo->updateOne($this->makePayload([
            'Filter' => $filter,
            'Update' => $update,
        ], $opts));
        return toPHP($data, ['root' => UpdateResult::class]);
    }

    public function updateMany($filter = [], $update = [], array $opts = []): UpdateResult
    {
        $filter = $this->sanitize($filter);
        $update = $this->sanitize($update);
        $data = $this->mongo->updateMany($this->makePayload([
            'Filter' => $filter,
            'Update' => $update,
        ], $opts));
        return toPHP($data, ['root' => UpdateResult::class]);
    }

    public function replaceOne($filter = [], $replace = [], array $opts = []): UpdateResult
    {
        $filter = $this->sanitize($filter);
        $replace = $this->sanitize($replace);
        $data = $this->mongo->replaceOne($this->makePayload([
            'Filter' => $filter,
            'Replace' => $replace,
        ], $opts));
        return toPHP($data, ['root' => UpdateResult::class]);
    }

    public function countDocuments($filter = [], array $opts = []): int
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->countDocuments($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return unpack('P', $data)[1];
    }

    public function deleteOne($filter = [], array $opts = []): DeleteResult
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->deleteOne($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return toPHP($data, ['root' => DeleteResult::class]);
    }

    public function deleteMany($filter = [], array $opts = []): DeleteResult
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->deleteMany($this->makePayload([
            'Filter' => $filter,
        ], $opts));
        return toPHP($data, ['root' => DeleteResult::class]);
    }

    public function aggregate($pipeline = [], array $opts = [])
    {
        $pipeline = $this->sanitize($pipeline);
        $data = $this->mongo->aggregate($this->makePayload([
            'Pipeline' => $pipeline,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data !== '' ? toPHP($data, $typeMap) : [];
    }

    public function bulkWrite($operations = [], array $opts = []): BulkWriteResult
    {
        $operations = $this->sanitize($operations);
        $data = $this->mongo->bulkWrite($this->makePayload([
            'Operations' => $operations,
        ], $opts));
        return toPHP($data, ['root' => BulkWriteResult::class]);
    }

    public function distinct(string $fieldName, $filter = [], array $opts = [])
    {
        $filter = $this->sanitize($filter);
        $data = $this->mongo->distinct($this->makePayload([
            'FieldName' => $fieldName,
            'Filter' => $filter,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data !== '' ? toPHP($data, $typeMap) : [];
    }

    public function createIndex($index = [], array $opts = []): string
    {
        $index = $this->sanitize($index);
        return $this->mongo->createIndex($this->makePayload([
            'IndexKeys' => $index,
        ], $opts));
    }

    public function createIndexes($indexes = [], array $opts = []): array
    {
        $indexes = $this->sanitize($indexes);
        $data = $this->mongo->createIndexes($this->makePayload([
            'Models' => $indexes,
        ], $opts));
        return $data === '' ? [] : toPHP($data, ['root' => 'array']);
    }

    public function listIndexes($indexes = [], array $opts = []): array
    {
        $data = $this->mongo->listIndexes($this->makePayload([], $opts));
        return $data === '' ? [] : toPHP($data, ['root' => 'array', 'document' => IndexInfo::class, 'fieldPaths' => ['$.key' => 'array']]);
    }

    public function dropIndex(string $name, array $opts = [])
    {
        $data = $this->mongo->dropIndex($this->makePayload([
            'Name' => $name,
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data === '' ? [] : toPHP($data, $typeMap);
    }

    public function dropIndexes(array $opts = [])
    {
        $data = $this->mongo->dropIndexes($this->makePayload([
        ], $opts));
        $typeMap = $opts['typeMap'] ?? $this->typeMap;
        return $data === '' ? [] : toPHP($data, $typeMap);
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
