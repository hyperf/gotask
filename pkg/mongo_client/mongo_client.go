package mongo_client

import (
	"context"
	"github.com/pkg/errors"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
	"time"
)

type MongoProxy struct {
	timeout time.Duration
	client  *mongo.Client
}

func NewMongoProxy(client *mongo.Client) *MongoProxy {
	return &MongoProxy{
		5 * time.Second,
		client,
	}
}

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

func (m *MongoProxy) InsertOne(payload InsertOneCmd, result *interface{}) error {
	doc, err := bson.Marshal(payload.Record)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.InsertOne(ctx, doc, payload.Opts...)
	return err
}

type InsertManyCmd struct {
	Database   string
	Collection string
	Records    []interface{}
	Opts       []*options.InsertManyOptions
}

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
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
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

func (m *MongoProxy) FindOne(payload FindOneCmd, result *map[string]interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	err = collection.FindOne(ctx, filter, payload.Opts...).Decode(result)
	return err
}

type FindCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.FindOptions
}

func (m *MongoProxy) Find(payload FindCmd, result *[]map[string]interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
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
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
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
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
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
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.ReplaceOne(ctx, filter, replace, payload.Opts...)
	return err
}

type CountDocumentsCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.CountOptions
}

func (m *MongoProxy) CountDocuments(payload CountDocumentsCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.CountDocuments(ctx, filter, payload.Opts...)
	return err
}

type DeleteOneCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.DeleteOptions
}

func (m *MongoProxy) DeleteOne(payload DeleteOneCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.DeleteOne(ctx, filter, payload.Opts...)
	return err
}

type DeleteManyCmd struct {
	Database   string
	Collection string
	Filter     interface{}
	Opts       []*options.DeleteOptions
}

func (m *MongoProxy) DeleteMany(payload DeleteManyCmd, result *interface{}) error {
	filter, err := bson.Marshal(payload.Filter)
	if err != nil {
		return errors.Wrap(err, "failed to marshal bson")
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.DeleteMany(ctx, filter, payload.Opts...)
	return err
}

type AggregateCmd struct {
	Database   string
	Collection string
	Pipeline   []interface{}
	Opts       []*options.AggregateOptions
}

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
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
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

func (m *MongoProxy) Drop(payload DropCmd, result *interface{}) error {
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	return collection.Drop(ctx)
}

type Cmd struct {
	Database string
	Command  interface{}
	Opts     []*options.RunCmdOptions
}

func (m *MongoProxy) RunCommand(payload Cmd, result *map[string]interface{}) error {
	cmd, err := bson.Marshal(payload.Command)
	if err != nil {
		return err
	}
	database := m.client.Database(payload.Database)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	return database.RunCommand(ctx, cmd, payload.Opts...).Decode(&result)
}

func (m *MongoProxy) RunCommandCursor(payload Cmd, result *[]map[string]interface{}) error {
	cmd, err := bson.Marshal(payload.Command)
	if err != nil {
		return err
	}
	database := m.client.Database(payload.Database)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	cursor, err := database.RunCommandCursor(ctx, cmd, payload.Opts...)
	if cursor != nil {
		return cursor.All(ctx, result)
	}
	if err != mongo.ErrNilCursor && err != mongo.ErrNilDocument {
		return errors.Wrap(err, "error while running command")
	}
	return nil
}
