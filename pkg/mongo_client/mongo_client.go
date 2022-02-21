package mongo_client

import (
	"context"
	"github.com/hyperf/gotask/v2/pkg/gotask"
	"time"

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

func (m *MongoProxy) getHandler(i interface{}, f coreExecutor) gotask.Handler {
	mw := stackMiddleware(i)
	return mw(func(cmd interface{}, result *interface{}) error {
		ctx, cancel := context.WithTimeout(context.Background(), m.timeout)
		defer cancel()
		return f(ctx, result)
	})
}

func (m *MongoProxy) exec(i interface{}, payload []byte, result *[]byte, f coreExecutor) error {
	var r interface{}
	defer func() {
		if r == nil {
			*result = nil
		} else {
			*result = r.([]byte)
		}
	}()
	return m.getHandler(i, f)(payload, &r)
}

type coreExecutor func(ctx context.Context, r *interface{}) error

type InsertOneCmd struct {
	Database   string
	Collection string
	Record     bson.Raw
	Opts       *options.InsertOneOptions
}

// InsertOne executes an insert command to insert a single document into the collection.
func (m *MongoProxy) InsertOne(payload []byte, result *[]byte) (err error) {
	cmd := &InsertOneCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) error {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.InsertOne(ctx, cmd.Record, cmd.Opts)
		return err
	})
}

type InsertManyCmd struct {
	Database   string
	Collection string
	Records    []interface{}
	Opts       *options.InsertManyOptions
}

// InsertMany executes an insert command to insert multiple documents into the collection. If write errors occur
// during the operation (e.g. duplicate key error), this method returns a BulkWriteException error.
func (m *MongoProxy) InsertMany(payload []byte, result *[]byte) (err error) {
	cmd := &InsertManyCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) error {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.InsertMany(ctx, cmd.Records, cmd.Opts)
		return err
	})
}

type FindOneCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Opts       *options.FindOneOptions
}

// FindOne executes a find command and returns one document in the collection.
func (m *MongoProxy) FindOne(payload []byte, result *[]byte) error {
	cmd := &FindOneCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		err = collection.FindOne(ctx, cmd.Filter, cmd.Opts).Decode(r)
		return err
	})
}

type FindOneAndDeleteCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Opts       *options.FindOneAndDeleteOptions
}

// FindOneAndDelete executes a findAndModify command to delete at most one document in the collection. and returns the
// document as it appeared before deletion.
func (m *MongoProxy) FindOneAndDelete(payload []byte, result *[]byte) error {
	cmd := &FindOneAndDeleteCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		err = collection.FindOneAndDelete(ctx, cmd.Filter, cmd.Opts).Decode(r)
		return err
	})
}

type FindOneAndUpdateCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Update     bson.Raw
	Opts       *options.FindOneAndUpdateOptions
}

// FindOneAndUpdate executes a findAndModify command to update at most one document in the collection and returns the
// document as it appeared before updating.
func (m *MongoProxy) FindOneAndUpdate(payload []byte, result *[]byte) error {
	cmd := &FindOneAndUpdateCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		err = collection.FindOneAndUpdate(ctx, cmd.Filter, cmd.Update, cmd.Opts).Decode(r)
		return err
	})
}

type FindOneAndReplaceCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Replace    bson.Raw
	Opts       *options.FindOneAndReplaceOptions
}

// FindOneAndReplace executes a findAndModify command to replace at most one document in the collection
// and returns the document as it appeared before replacement.
func (m *MongoProxy) FindOneAndReplace(payload []byte, result *[]byte) error {
	cmd := &FindOneAndReplaceCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		err = collection.FindOneAndReplace(ctx, cmd.Filter, cmd.Replace, cmd.Opts).Decode(r)
		return err
	})
}

type FindCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Opts       *options.FindOptions
}

// Find executes a find command and returns all the matching documents in the collection.
func (m *MongoProxy) Find(payload []byte, result *[]byte) error {
	cmd := &FindCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) error {
		var rr []interface{}
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		cursor, err := collection.Find(ctx, cmd.Filter, cmd.Opts)
		if cursor != nil {
			defer cursor.Close(ctx)
			err = cursor.All(ctx, &rr)
		}
		*r = rr
		return err
	})
}

type UpdateOneCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Update     bson.Raw
	Opts       *options.UpdateOptions
}

// UpdateOne executes an update command to update at most one document in the collection.
func (m *MongoProxy) UpdateOne(payload []byte, result *[]byte) error {
	cmd := &UpdateOneCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.UpdateOne(ctx, cmd.Filter, cmd.Update, cmd.Opts)
		return err
	})
}

type UpdateManyCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Update     bson.Raw
	Opts       *options.UpdateOptions
}

// UpdateMany executes an update command to update documents in the collection.
func (m *MongoProxy) UpdateMany(payload []byte, result *[]byte) error {
	cmd := &UpdateManyCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.UpdateMany(ctx, cmd.Filter, cmd.Update, cmd.Opts)
		return err
	})
}

type ReplaceOneCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Replace    bson.Raw
	Opts       *options.ReplaceOptions
}

// ReplaceOne executes an update command to replace at most one document in the collection.
func (m *MongoProxy) ReplaceOne(payload []byte, result *[]byte) error {
	cmd := &ReplaceOneCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.ReplaceOne(ctx, cmd.Filter, cmd.Replace, cmd.Opts)
		return err
	})
}

type CountDocumentsCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Opts       *options.CountOptions
}

// CountDocuments returns the number of documents in the collection.
func (m *MongoProxy) CountDocuments(payload []byte, result *[]byte) error {
	cmd := &CountDocumentsCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.CountDocuments(ctx, cmd.Filter, cmd.Opts)
		return err
	})
}

type DeleteOneCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Opts       *options.DeleteOptions
}

// DeleteOne executes a delete command to delete at most one document from the collection.
func (m *MongoProxy) DeleteOne(payload []byte, result *[]byte) error {
	cmd := &DeleteOneCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.DeleteOne(ctx, cmd.Filter, cmd.Opts)
		return err
	})
}

type DeleteManyCmd struct {
	Database   string
	Collection string
	Filter     bson.Raw
	Opts       *options.DeleteOptions
}

// DeleteMany executes a delete command to delete documents from the collection.
func (m *MongoProxy) DeleteMany(payload []byte, result *[]byte) error {
	cmd := &DeleteManyCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.DeleteMany(ctx, cmd.Filter, cmd.Opts)
		return err
	})
}

type AggregateCmd struct {
	Database   string
	Collection string
	Pipeline   mongo.Pipeline
	Opts       *options.AggregateOptions
}

// Aggregate executes an aggregate command against the collection and returns all the resulting documents.
func (m *MongoProxy) Aggregate(payload []byte, result *[]byte) error {
	cmd := &AggregateCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		var rr []interface{}
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		cursor, err := collection.Aggregate(ctx, cmd.Pipeline, cmd.Opts)
		if cursor != nil {
			defer cursor.Close(ctx)
			err = cursor.All(ctx, &rr)
		}
		*r = rr
		return err
	})
}

type BulkWriteCmd struct {
	Database   string
	Collection string
	Operations []map[string][]bson.Raw
	Opts       *options.BulkWriteOptions
}

func (m *MongoProxy) BulkWrite(payload []byte, result *[]byte) error {
	cmd := &BulkWriteCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		models := parseModels(cmd.Operations)
		*r, err = collection.BulkWrite(ctx, models, cmd.Opts)
		return err
	})
}

type DistinctCmd struct {
	Database   string
	Collection string
	FieldName  string
	Filter     bson.Raw
	Opts       *options.DistinctOptions
}

// Distinct executes a distinct command to find the unique values for a specified field in the collection.
func (m *MongoProxy) Distinct(payload []byte, result *[]byte) error {
	cmd := &DistinctCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		var rr []interface{}
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		rr, err = collection.Distinct(ctx, cmd.FieldName, cmd.Filter, cmd.Opts)
		*r = rr
		return err
	})
}

type CreateIndexCmd struct {
	Database   string
	Collection string
	IndexKeys  bson.Raw
	Opts       *options.IndexOptions
	CreateOpts *options.CreateIndexesOptions
}

func (m *MongoProxy) CreateIndex(payload []byte, result *[]byte) error {
	cmd := &CreateIndexCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		model := mongo.IndexModel{
			Keys:    cmd.IndexKeys,
			Options: cmd.Opts,
		}
		*r, err = collection.Indexes().CreateOne(ctx, model, cmd.CreateOpts)
		return err
	})
}

type CreateIndexesCmd struct {
	Database   string
	Collection string
	Models     []mongo.IndexModel
	Opts       *options.IndexOptions
	CreateOpts *options.CreateIndexesOptions
}

func (m *MongoProxy) CreateIndexes(payload []byte, result *[]byte) error {
	cmd := &CreateIndexesCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.Indexes().CreateMany(ctx, cmd.Models, cmd.CreateOpts)
		return err
	})
}

type DropIndexCmd struct {
	Database   string
	Collection string
	Name       string
	Opts       *options.DropIndexesOptions
}

func (m *MongoProxy) DropIndex(payload []byte, result *[]byte) error {
	cmd := &DropIndexCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.Indexes().DropOne(ctx, cmd.Name, cmd.Opts)
		return err
	})
}

type DropIndexesCmd struct {
	Database   string
	Collection string
	Opts       *options.DropIndexesOptions
}

func (m *MongoProxy) DropIndexes(payload []byte, result *[]byte) error {
	cmd := &DropIndexesCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		*r, err = collection.Indexes().DropAll(ctx, cmd.Opts)
		return err
	})
}

type ListIndexesCmd struct {
	Database   string
	Collection string
	Opts       *options.ListIndexesOptions
}

func (m *MongoProxy) ListIndexes(payload []byte, result *[]byte) error {
	cmd := &ListIndexesCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		var rr []interface{}
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		cursor, err := collection.Indexes().List(ctx, cmd.Opts)
		if cursor != nil {
			defer cursor.Close(ctx)
			err = cursor.All(ctx, &rr)
			*r = rr
		}
		return err
	})
}

type DropCmd struct {
	Database   string
	Collection string
}

// Drop drops the collection on the server. This method ignores "namespace not found" errors so it is safe to drop
// a collection that does not exist on the server.
func (m *MongoProxy) Drop(payload []byte, result *[]byte) error {
	cmd := &DropCmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		collection := m.client.Database(cmd.Database).Collection(cmd.Collection)
		return collection.Drop(ctx)
	})
}

type Cmd struct {
	Database string
	Command  bson.D
	Opts     *options.RunCmdOptions
}

// RunCommand executes the given command against the database.
func (m *MongoProxy) RunCommand(payload []byte, result *[]byte) error {
	cmd := &Cmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		database := m.client.Database(cmd.Database)
		return database.RunCommand(ctx, cmd.Command, cmd.Opts).Decode(r)
	})
}

// RunCommandCursor executes the given command against the database and parses the response as a slice. If the command
// being executed does not return a slice, the command will be executed on the server and an error
// will be returned because the server response cannot be parsed as a slice.
func (m *MongoProxy) RunCommandCursor(payload []byte, result *[]byte) error {
	cmd := &Cmd{}
	return m.exec(cmd, payload, result, func(ctx context.Context, r *interface{}) (err error) {
		var rr []interface{}
		database := m.client.Database(cmd.Database)
		cursor, err := database.RunCommandCursor(ctx, cmd.Command, cmd.Opts)
		if cursor != nil {
			defer cursor.Close(ctx)
			err = cursor.All(ctx, &rr)
		}
		*r = rr
		return err
	})
}
