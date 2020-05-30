package mongo_client

import (
	"context"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
	"time"
)

type Mongo struct {
	timeout time.Duration
	client  *mongo.Client
}

func NewMongo(client *mongo.Client) *Mongo {
	return &Mongo{
		5 * time.Second,
		client,
	}
}

func NewMongoWithTimeout(client *mongo.Client, timeout time.Duration) *Mongo {
	return &Mongo{
		timeout,
		client,
	}
}

type InsertOneCmd struct {
	Database   string
	Collection string
	Record     []byte
	Opts       []*options.InsertOneOptions
}

func (m *Mongo) InsertOne(payload InsertOneCmd, result *interface{}) error {
	var doc bson.D
	err := bson.UnmarshalExtJSON(payload.Record, false, &doc)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	res, err := collection.InsertOne(ctx, doc, payload.Opts...)
	if err != nil {
		return err
	}
	*result = res.InsertedID
	return nil
}

type InsertManyCmd struct {
	Database   string
	Collection string
	Records    [][]byte
	Opts       []*options.InsertManyOptions
}

func (m *Mongo) InsertMany(payload InsertManyCmd, result *interface{}) error {
	var docs []interface{}
	for _, v := range payload.Records {
		var doc bson.D
		err := bson.UnmarshalExtJSON(v, false, &doc)
		if err != nil {
			return err
		}
		docs = append(docs, doc)
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	res, err := collection.InsertMany(ctx, docs, payload.Opts...)
	if err != nil {
		return err
	}
	*result = res.InsertedIDs
	return nil
}

type FindOneCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Opts       []*options.FindOneOptions
}

func (m *Mongo) FindOne(payload FindOneCmd, result *interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	err = collection.FindOne(ctx, filter, payload.Opts...).Decode(result)
	return err
}

type FindCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Opts       []*options.FindOptions
}

func (m *Mongo) Find(payload FindCmd, result *[]interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	cursor, err := collection.Find(ctx, filter, payload.Opts...)
	if cursor != nil {
		return cursor.All(ctx, result)
	}
	return nil

}

type UpdateOneCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Updates    []byte
	Opts       []*options.UpdateOptions
}

func (m *Mongo) UpdateOne(payload UpdateOneCmd, result *interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	var updates bson.D
	err = bson.UnmarshalExtJSON(payload.Updates, false, &updates)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.UpdateOne(ctx, filter, updates, payload.Opts...)
	return err
}

type UpdateManyCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Updates    []byte
	Opts       []*options.UpdateOptions
}

func (m *Mongo) UpdateMany(payload UpdateManyCmd, result *interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	var updates bson.D
	err = bson.UnmarshalExtJSON(payload.Updates, false, &updates)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.UpdateMany(ctx, filter, updates, payload.Opts...)
	return err
}

type ReplaceOneCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Replaces   []byte
	Opts       []*options.ReplaceOptions
}

func (m *Mongo) ReplaceOne(payload ReplaceOneCmd, result *interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	var replaces bson.D
	err = bson.UnmarshalExtJSON(payload.Replaces, false, &replaces)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.ReplaceOne(ctx, filter, replaces, payload.Opts...)
	return err
}

type CountDocumentsCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Replaces   []byte
	Opts       []*options.CountOptions
}

func (m *Mongo) CountDocuments(payload CountDocumentsCmd, result *interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.CountDocuments(ctx, filter, payload.Opts...)
	return err
}

type DeleteOneCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Opts       []*options.DeleteOptions
}

func (m *Mongo) DeleteOne(payload DeleteOneCmd, result *interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.DeleteOne(ctx, filter, payload.Opts...)
	return err
}

type DeleteManyCmd struct {
	Database   string
	Collection string
	Filter     []byte
	Opts       []*options.DeleteOptions
}

func (m *Mongo) DeleteMany(payload DeleteManyCmd, result *interface{}) error {
	var filter bson.M
	err := bson.UnmarshalExtJSON(payload.Filter, false, &filter)
	if err != nil {
		return err
	}
	collection := m.client.Database(payload.Database).Collection(payload.Collection)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	*result, err = collection.DeleteMany(ctx, filter, payload.Opts...)
	return err
}

type Cmd struct {
	Database string
	Cmd      []byte
	Opts     []*options.RunCmdOptions
}

func (m *Mongo) RunCommand(payload Cmd, result *interface{}) error {
	var cmd bson.D
	err := bson.UnmarshalExtJSON(payload.Cmd, false, &cmd)
	if err != nil {
		return err
	}
	database := m.client.Database(payload.Database)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	return database.RunCommand(ctx, cmd, payload.Opts...).Decode(&result)
}

func (m *Mongo) RunCommandCursor(payload Cmd, result *[]interface{}) error {
	var cmd bson.D
	err := bson.UnmarshalExtJSON(payload.Cmd, false, &cmd)
	if err != nil {
		return err
	}
	database := m.client.Database(payload.Database)
	ctx, _ := context.WithTimeout(context.Background(), m.timeout)
	cursor, err := database.RunCommandCursor(ctx, cmd, payload.Opts...)
	if cursor != nil {
		return cursor.All(ctx, result)
	}
	return nil
}
