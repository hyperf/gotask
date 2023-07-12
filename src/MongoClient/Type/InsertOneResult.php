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

use MongoDB\BSON\ObjectId;

class InsertOneResult implements \MongoDB\BSON\Unserializable
{
    private ?ObjectId $insertedId;

    public function bsonUnserialize(array $data): void
    {
        $this->insertedId = $data['insertedid'];
    }

    public function getInsertedId(): ?ObjectId
    {
        return $this->insertedId;
    }
}
