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

use Hyperf\GoTask\GoTaskProxy;

class MongoProxy extends GoTaskProxy
{
    /**
     * @param  $payload
     * @return mixed
     */
    public function aggregate($payload)
    {
        return parent::call('MongoProxy.Aggregate', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function countDocuments($payload)
    {
        return parent::call('MongoProxy.CountDocuments', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function deleteMany($payload)
    {
        return parent::call('MongoProxy.DeleteMany', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function deleteOne($payload)
    {
        return parent::call('MongoProxy.DeleteOne', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function drop($payload)
    {
        return parent::call('MongoProxy.Drop', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function find($payload)
    {
        return parent::call('MongoProxy.Find', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function findOne($payload)
    {
        return parent::call('MongoProxy.FindOne', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function insertMany($payload)
    {
        return parent::call('MongoProxy.InsertMany', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function insertOne($payload)
    {
        return parent::call('MongoProxy.InsertOne', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function replaceOne($payload)
    {
        return parent::call('MongoProxy.ReplaceOne', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function runCommand($payload)
    {
        return parent::call('MongoProxy.RunCommand', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function runCommandCursor($payload)
    {
        return parent::call('MongoProxy.RunCommandCursor', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function updateMany($payload)
    {
        return parent::call('MongoProxy.UpdateMany', $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function updateOne($payload)
    {
        return parent::call('MongoProxy.UpdateOne', $payload, 0);
    }
}
