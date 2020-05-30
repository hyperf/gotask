package main

import (
	"context"
	"github.com/reasno/gotask/pkg/config"
	"github.com/reasno/gotask/pkg/gotask"
	"github.com/reasno/gotask/pkg/mongo_client"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
	"log"
	"time"
)

func main() {
	connectTimeout, err := config.GetInt("mongodb.connectTimeout", 5_000)
	readWriteTimeout, err := config.GetInt("mongodb.operationTimeout", 60_000)
	uri, err := config.GetString("mongodb.uri", "mongodb://localhost:23173")
	ctx, _ := context.WithTimeout(context.Background(), time.Duration(connectTimeout))
	client, err := mongo.Connect(ctx, options.Client().ApplyURI(uri))
	if err != nil {
		panic(err)
	}
	if err := gotask.Register(mongo_client.NewMongoWithTimeout(client, time.Duration(readWriteTimeout))); err != nil {
		log.Fatalln(err)
	}
	if err := gotask.Run(); err != nil {
		log.Fatalln(err)
	}
}
