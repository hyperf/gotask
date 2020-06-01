package mongo_client

import (
	"os"
	"strconv"
	"time"

	"github.com/hyperf/gotask/v2/pkg/config"
)

type Config struct {
	Uri              string
	ConnectTimeout   time.Duration
	ReadWriteTimeout time.Duration
}

// LoadConfig loads Configurations from environmental variables or config file in PHP.
// Environmental variables takes priority.
func LoadConfig() Config {
	uri, ok := os.LookupEnv("MONGODB_URI")
	if !ok {
		uri, _ = config.GetString("mongodb.uri", "mongodb://localhost:27017")
	}
	readWriteTimeoutStr, ok := os.LookupEnv("MONGODB_READ_WRITE_TIMEOUT")
	readWriteTimeout, err := strconv.Atoi(readWriteTimeoutStr)
	if !ok || err != nil {
		readWriteTimeout, _ = config.GetInt("mongodb.read_write_timeout", 60_000)
	}
	connectTimeoutStr, ok := os.LookupEnv("MONGODB_CONNECT_TIMEOUT")
	connectTimeout, err := strconv.Atoi(connectTimeoutStr)
	if !ok || err != nil {
		readWriteTimeout, _ = config.GetInt("mongodb.connect_timeout", 3_000)
	}
	return Config{
		uri,
		time.Duration(connectTimeout) * time.Millisecond,
		time.Duration(readWriteTimeout) * time.Millisecond,
	}

}
