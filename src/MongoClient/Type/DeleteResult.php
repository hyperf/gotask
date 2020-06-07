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

class DeleteResult implements Unserializable
{
    /**
     * @var int
     */
    private $n;

    public function bsonUnserialize(array $data)
    {
        $this->n = $data['n'];
    }

    public function getDeletedCount(): int
    {
        return $this->n;
    }
}
