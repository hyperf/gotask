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
namespace Hyperf\GoTask\MongoClient\Type;

use MongoDB\BSON\Unserializable;

class UpdateResult implements Unserializable
{
    /**
     * @var int
     */
    private $matchedCount;

    /**
     * @var int
     */
    private $modifiedCount;

    /**
     * @var int
     */
    private $upsertedCount;

    /**
     * @var null|string
     */
    private $upsertedId;

    public function bsonUnserialize(array $data)
    {
        $this->matchedCount = $data['matchedcount'];
        $this->modifiedCount = $data['modifiedcount'];
        $this->upsertedCount = $data['upsertedcount'];
        $this->upsertedId = $data['upsertedid'];
    }

    /**
     * @return mixed
     */
    public function getUpsertedId(): ?string
    {
        return $this->upsertedId;
    }

    public function getUpsertedCount(): int
    {
        return $this->upsertedCount;
    }

    public function getModifiedCount(): int
    {
        return $this->modifiedCount;
    }

    public function getMatchedCount(): int
    {
        return $this->matchedCount;
    }
}
