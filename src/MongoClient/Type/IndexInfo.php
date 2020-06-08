<?php


namespace Hyperf\GoTask\MongoClient\Type;


use MongoDB\BSON\Unserializable;

class IndexInfo implements Unserializable
{

    /**
     * @var int
     */
    private $v;
    /**
     * @var array
     */
    private $key;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $ns;
    /**
     * @var bool
     */
    private $sparse;
    /**
     * @var bool
     */
    private $unique;
    /**
     * @var bool
     */
    private $ttl;

    public function bsonUnserialize(array $data)
    {
        $this->v = $data['v'] ?? 0;
        $this->key = $data['key'] ?? [];
        $this->name = $data['name'] ?? '';
        $this->ns = $data['ns'] ?? '';
        $this->sparse = $data['sparse'] ?? false;
        $this->unique = $data['unique'] ?? false;
        $this->ttl = $data['ttl'] ?? false;
    }

    /**
     * @return array
     */
    public function getKey(): array
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->v;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->ns;
    }

    public function isSparse(): bool
    {
        return $this->sparse;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function isTtl(): bool
    {
        return $this->ttl;
    }
}