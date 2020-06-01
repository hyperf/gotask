package main

import (
	"context"
	"log"

	"github.com/hyperf/gotask/v2/pkg/gotask"
	"github.com/hyperf/gotask/v2/pkg/mongo_client"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

func main() {
	mongoConfig := mongo_client.LoadConfig()
	ctx, cancel := context.WithTimeout(context.Background(), mongoConfig.ConnectTimeout)
	defer cancel()

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
