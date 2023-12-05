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

use Hyperf\GoTask\GoTask;
use Hyperf\GoTask\GoTaskProxy;

class MongoProxy extends GoTaskProxy
{
    public function aggregate(string $payload): string
    {
        return (string) parent::call('MongoProxy.Aggregate', $payload, GoTask::PAYLOAD_RAW);
    }

    public function bulkWrite(string $payload): string
    {
        return (string) parent::call('MongoProxy.BulkWrite', $payload, GoTask::PAYLOAD_RAW);
    }

    public function countDocuments(string $payload): string
    {
        return (string) parent::call('MongoProxy.CountDocuments', $payload, GoTask::PAYLOAD_RAW);
    }

    public function createIndex(string $payload): string
    {
        return (string) parent::call('MongoProxy.CreateIndex', $payload, GoTask::PAYLOAD_RAW);
    }

    public function createIndexes(string $payload): string
    {
        return (string) parent::call('MongoProxy.CreateIndexes', $payload, GoTask::PAYLOAD_RAW);
    }

    public function deleteMany(string $payload): string
    {
        return (string) parent::call('MongoProxy.DeleteMany', $payload, GoTask::PAYLOAD_RAW);
    }

    public function deleteOne(string $payload): string
    {
        return (string) parent::call('MongoProxy.DeleteOne', $payload, GoTask::PAYLOAD_RAW);
    }

    public function distinct(string $payload): string
    {
        return (string) parent::call('MongoProxy.Distinct', $payload, GoTask::PAYLOAD_RAW);
    }

    public function drop(string $payload): string
    {
        return (string) parent::call('MongoProxy.Drop', $payload, GoTask::PAYLOAD_RAW);
    }

    public function dropIndex(string $payload): string
    {
        return (string) parent::call('MongoProxy.DropIndex', $payload, GoTask::PAYLOAD_RAW);
    }

    public function dropIndexes(string $payload): string
    {
        return (string) parent::call('MongoProxy.DropIndexes', $payload, GoTask::PAYLOAD_RAW);
    }

    public function find(string $payload): string
    {
        return (string) parent::call('MongoProxy.Find', $payload, GoTask::PAYLOAD_RAW);
    }

    public function findOne(string $payload): string
    {
        return (string) parent::call('MongoProxy.FindOne', $payload, GoTask::PAYLOAD_RAW);
    }

    public function findOneAndDelete(string $payload): string
    {
        return (string) parent::call('MongoProxy.FindOneAndDelete', $payload, GoTask::PAYLOAD_RAW);
    }

    public function findOneAndReplace(string $payload): string
    {
        return (string) parent::call('MongoProxy.FindOneAndReplace', $payload, GoTask::PAYLOAD_RAW);
    }

    public function findOneAndUpdate(string $payload): string
    {
        return (string) parent::call('MongoProxy.FindOneAndUpdate', $payload, GoTask::PAYLOAD_RAW);
    }

    public function insertMany(string $payload): string
    {
        return (string) parent::call('MongoProxy.InsertMany', $payload, GoTask::PAYLOAD_RAW);
    }

    public function insertOne(string $payload): string
    {
        return (string) parent::call('MongoProxy.InsertOne', $payload, GoTask::PAYLOAD_RAW);
    }

    public function listIndexes(string $payload): string
    {
        return (string) parent::call('MongoProxy.ListIndexes', $payload, GoTask::PAYLOAD_RAW);
    }

    public function replaceOne(string $payload): string
    {
        return (string) parent::call('MongoProxy.ReplaceOne', $payload, GoTask::PAYLOAD_RAW);
    }

    public function runCommand(string $payload): string
    {
        return (string) parent::call('MongoProxy.RunCommand', $payload, GoTask::PAYLOAD_RAW);
    }

    public function runCommandCursor(string $payload): string
    {
        return (string) parent::call('MongoProxy.RunCommandCursor', $payload, GoTask::PAYLOAD_RAW);
    }

    public function updateMany(string $payload): string
    {
        return (string) parent::call('MongoProxy.UpdateMany', $payload, GoTask::PAYLOAD_RAW);
    }

    public function updateOne(string $payload): string
    {
        return (string) parent::call('MongoProxy.UpdateOne', $payload, GoTask::PAYLOAD_RAW);
    }
}
