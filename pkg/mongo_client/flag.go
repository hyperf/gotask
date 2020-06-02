package mongo_client

import (
	"flag"
	"os"
	"time"
)

func init() {
	parseConfig()
}

func parseConfig() {
	uri, ok := os.LookupEnv("MONGODB_URI")
	if !ok {
		uri = "mongodb://127.0.0.1:27017"
	}
	ct := getTimeout("MONGODB_CONNECT_TIMEOUT", 3*time.Second)
	rwt := getTimeout("MONGODB_READ_WRITE_TIMEOUT", time.Minute)

	globalMongoUri = flag.String("mongodb-uri", uri, "the default mongodb uri")
	globalMongoConnectTimeout = flag.Duration("mongodb-connect-timeout", ct, "mongodb connect timeout")
	globalMongoReadWriteTimeout = flag.Duration("mongodb-read-write-timeout", rwt, "mongodb read write timeout")
}
