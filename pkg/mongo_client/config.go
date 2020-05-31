package mongo_client

import (
	"github.com/reasno/gotask/pkg/config"
	"os"
	"strconv"
	"time"
)

type Config struct {
	Uri              string
	ConnectTimeout   time.Duration
	ReadWriteTimeout time.Duration
}

func LoadConfig() Config {
	uri, ok := os.LookupEnv("MONGODB_URI")
	if !ok {
		uri, _ = config.GetString("mongodb.uri", "mongodb://localhost:27017")
	}
	readWriteTimeoutStr, ok := os.LookupEnv("MONGODB_URI_READ_WRITE_TIMEOUT")
	readWriteTimeout, err := strconv.Atoi(readWriteTimeoutStr)
	if !ok || err != nil {
		readWriteTimeout, _ = config.GetInt("mongodb.ReadWriteTimeout", 60_000)
	}
	connectTimeoutStr, ok := os.LookupEnv("MONGODB_URI_CONNECT_TIMEOUT")
	connectTimeout, err := strconv.Atoi(connectTimeoutStr)
	if !ok || err != nil {
		readWriteTimeout, _ = config.GetInt("mongodb.ReadWriteTimeout", 60_000)
	}
	return Config{
		uri,
		time.Duration(connectTimeout) * time.Millisecond,
		time.Duration(readWriteTimeout) * time.Millisecond,
	}

}
