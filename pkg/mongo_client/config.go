package mongo_client

import (
	"flag"
	"os"
	"time"
)

type Config struct {
	Uri              string
	ConnectTimeout   time.Duration
	ReadWriteTimeout time.Duration
}

var (
	globalMongoUri              *string
	globalMongoConnectTimeout   *time.Duration
	globalMongoReadWriteTimeout *time.Duration
)

func init() {
	uri, ok := os.LookupEnv("MONGODB_URI")
	if !ok {
		uri = "mongodb://127.0.0.1:27017"
	}
	ct := getTimeout("MONGODB_CONNECT_TIMEOUT", 3*time.Second)
	rwt := getTimeout("MONGODB_READ_WRITE_TIMEOUT", time.Minute)

	globalMongoUri = flag.String("mongodb-uri", uri, "the default mongodb uri")
	globalMongoConnectTimeout = flag.Duration("mongodb-connect-timeout", ct, "mongodb connect timeout")
	globalMongoReadWriteTimeout = flag.Duration("mongodb-read-write-timeout", rwt, "mongodb read write timeout")
	flag.Parse()
}

func getTimeout(env string, fallback time.Duration) (result time.Duration) {
	env, ok := os.LookupEnv(env)
	if !ok {
		return fallback
	}
	result, err := time.ParseDuration(env)
	if err != nil {
		return fallback
	}
	return result
}

// LoadConfig loads Configurations from environmental variables or config file in PHP.
// Environmental variables takes priority.
func LoadConfig() Config {
	return Config{
		*globalMongoUri,
		*globalMongoConnectTimeout,
		*globalMongoReadWriteTimeout,
	}
}
