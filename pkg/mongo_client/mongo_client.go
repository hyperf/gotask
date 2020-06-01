package mongo_client

import (
	"context"
	"time"

	"github.com/pkg/errors"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

type MongoProxy struct {
	timeout time.Duration
	client  *mongo.Client
}

// NewMongoProxy creates a new Mongo Proxy
func NewMongoProxy(client *mongo.Client) *MongoProxy {
	return &MongoProxy{
		5 * time.Second,
		client,
	}
}

// NewMongoProxyWithTimeout creates a new Mongo Proxy, with a read write timeout.
func NewMongoProxyWithTimeout(client *mongo.Client, timeout time.Duration) *MongoProxy {
	return &MongoProxy{
		timeout,
		client,
	}
}

type InsertOneCmd struct {
	Database   string
	Collection string
	Record     interface{}
	Opts       []*options.InsertOneOptions
}

// InsertOne executes an insert command to insert a single document into the collection.
func (m *MongoProxy) InsertOne(payload InsertOneCmd, result *interface{}) error {
	doc, err := bson.Marshal(payload.Record)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	*result, err = collection.InsertOne(ctx, doc, payload.Opts...)
	return err
}

type InsertManyCmd struct {
	Database   string
	Collection string
	Records    []interface{}
	Opts       []*options.InsertManyOptions
}

// InsertMany executes an insert command to insert multiple documents into the collection. If write errors occur
// during the operation (e.g. duplicate key error), this method returns a BulkWriteException error.
func (m *MongoProxy) InsertMany(payload InsertManyCmd, result *interface{}) error {
	var docs []interface{}
	for _, v := range payload.Records {
		doc, err := bson.Marshal(v)
		if err != nil {
			return errors.Wrap(err, "failed to marshal bson")
		}
		docs = append(docs, doc)
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	var err error
	*result, err = collection.InsertMany(ctx, docs, payload.Opts...)
	return err
}

type FindOneCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.FindOneOptions
}

// FindOne executes a find command and returns one document in the collection.
func (m *MongoProxy) FindOne(payload FindOneCmd, result *map[string]interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	err = collection.FindOne(ctx, filter, payload.Opts...).Decode(result)
	return err
}

type FindCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.FindOptions
}

// Find executes a find command and returns all the matching documents in the collection.
func (m *MongoProxy) Find(payload FindCmd, result *[]map[string]interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	cursor, err := collection.Find(ctx, filter, payload.Opts...)
	if cursor != nil {
		return cursor.All(ctx, result)
	}
	if err != mongo.ErrNilCursor && err != mongo.ErrNilDocument {
		return errors.Wrap(err, "error while finding")
	}
	return nil

}

type UpdateOneCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Update     interface{}
	Opts       []*options.UpdateOptions
}

// UpdateOne executes an update command to update at most one document in the collection.
func (m *MongoProxy) UpdateOne(payload UpdateOneCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	update, err := bson.Marshal(payload.Update)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	*result, err = collection.UpdateOne(ctx, filter, update, payload.Opts...)
	return err
}

type UpdateManyCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Update     interface{}
	Opts       []*options.UpdateOptions
}

// UpdateMany executes an update command to update documents in the collection.
func (m *MongoProxy) UpdateMany(payload UpdateManyCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	update, err := bson.Marshal(payload.Update)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	*result, err = collection.UpdateMany(ctx, filter, update, payload.Opts...)
	return err
}

type ReplaceOneCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Replace    interface{}
	Opts       []*options.ReplaceOptions
}

// ReplaceOne executes an update command to replace at most one document in the collection.
func (m *MongoProxy) ReplaceOne(payload ReplaceOneCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	replace, err := bson.Marshal(payload.Replace)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	*result, err = collection.ReplaceOne(ctx, filter, replace, payload.Opts...)
	return err
}

type CountDocumentsCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.CountOptions
}

// CountDocuments returns the number of documents in the collection.
func (m *MongoProxy) CountDocuments(payload CountDocumentsCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	*result, err = collection.CountDocuments(ctx, filter, payload.Opts...)
	return err
}

type DeleteOneCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.DeleteOptions
}

// DeleteOne executes a delete command to delete at most one document from the collection.
func (m *MongoProxy) DeleteOne(payload DeleteOneCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	*result, err = collection.DeleteOne(ctx, filter, payload.Opts...)
	return err
}

type DeleteManyCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.DeleteOptions
}

// DeleteMany executes a delete command to delete documents from the collection.
func (m *MongoProxy) DeleteMany(payload DeleteManyCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	*result, err = collection.DeleteMany(ctx, filter, payload.Opts...)
	return err
}

type AggregateCmd struct {
	Database   string
	Collection string
	Pipeline   []interface{}
	Opts       []*options.AggregateOptions
}

// Aggregate executes an aggregate command against the collection and returns all the resulting documents.
func (m *MongoProxy) Aggregate(payload AggregateCmd, result *[]map[string]interface{}) error {
	var pipeline = make([][]byte, len(payload.Pipeline))
	for _, step := range payload.Pipeline {
		s, err := bson.Marshal(step)
		if err != nil {
			return errors.Wrap(err, "failed to marshal bson")
		}
		pipeline = append(pipeline, s)
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	cursor, err := collection.Aggregate(ctx, payload.Pipeline, payload.Opts...)
	if cursor != nil {
		return cursor.All(ctx, result)
	}
	if err != mongo.ErrNilCursor && err != mongo.ErrNilDocument {
		return errors.Wrap(err, "error while aggregating")
	}
	return nil

}

type DropCmd struct {
	Database   string
	Collection string
}

// Drop drops the collection on the server. This method ignores "namespace not found" errors so it is safe to drop
// a collection that does not exist on the server.
func (m *MongoProxy) Drop(payload DropCmd, result *interface{}) error {
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	return collection.Drop(ctx)
}

type Cmd struct {
	Database string
	Command  interface{}
	Opts     []*options.RunCmdOptions
}

// RunCommand executes the given command against the database.
func (m *MongoProxy) RunCommand(payload Cmd, result *map[string]interface{}) error {
	cmd, err := bson.Marshal(payload.Command)
	if err != nil {
		return err
	}
	database := m.client.Database(payload.Database)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	return database.RunCommand(ctx, cmd, payload.Opts...).Decode(&result)
}

// RunCommandCursor executes the given command against the database and parses the response as a slice. If the command
// being executed does not return a slice, the command will be executed on the server and an error
// will be returned because the server response cannot be parsed as a slice.
func (m *MongoProxy) RunCommandCursor(payload Cmd, result *[]map[string]interface{}) error {
	cmd, err := bson.Marshal(payload.Command)
	if err != nil {
		return err
	}
	database := m.client.Database(payload.Database)
	ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
	defer cancel()
	cursor, err := database.RunCommandCursor(ctx, cmd, payload.Opts...)
	if cursor != nil {
		return cursor.All(ctx, result)
	}
	if err != mongo.ErrNilCursor && err != mongo.ErrNilDocument {
		return errors.Wrap(err, "error while running command")
	}
	return nil
}
