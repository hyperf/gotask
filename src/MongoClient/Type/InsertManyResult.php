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
use MongoDB\BSON\Unserializable;

class InsertManyResult implements Unserializable
{
    /**
     * @var array<ObjectId>
     */
    private $insertedIDs;

    public function bsonUnserialize(array $data)
    {
        $this->insertedIDs = $data['insertedids'];
    }

    /**
     * @return array<ObjectId>
     */
    public function getInsertedIDs(): array
    {
        return $this->insertedIDs;
    }
}
