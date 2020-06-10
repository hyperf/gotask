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
	if !flag.Parsed() {
		flag.Parse()
	}
	return Config{
		*globalMongoUri,
		*globalMongoConnectTimeout,
		*globalMongoReadWriteTimeout,
	}
}
