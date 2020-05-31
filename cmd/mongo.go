package main

import (
	"context"
	"github.com/reasno/gotask/pkg/gotask"
	"github.com/reasno/gotask/pkg/mongo_client"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
	"log"
)

func main() {
	mongoConfig := mongo_client.LoadConfig()
	ctx, _ := context.WithTimeout(context.Background(), mongoConfig.ConnectTimeout)

	client, err := mongo.Connect(ctx, options.Client().ApplyURI(mongoConfig.Uri))
	if err != nil {
		log.Fatalln(err)
	}

	if err := gotask.Register(mongo_client.NewMongoProxyWithTimeout(client, mongoConfig.ReadWriteTimeout)); err != nil {
		log.Fatalln(err)
	}

	if err := gotask.Run(); err != nil {
		log.Fatalln(err)
	}
}
