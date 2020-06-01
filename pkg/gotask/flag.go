package gotask

import (
	"flag"
)

var (
	address       *string
	standalone    *bool
	listenOnPipe  *bool
	go2phpAddress *string
	reflection    *bool
)

func init() {
	standalone = flag.Bool("standalone", false, "if set, ignore parent process status")
	address = flag.String("address", "127.0.0.1:6001", "must be a unix socket or tcp address:port like 127.0.0.1:6001")
	listenOnPipe = flag.Bool("listen-on-pipe", false, "listen on stdin/stdout pipe")
	go2phpAddress = flag.String("go2php-address", "127.0.0.1:6002", "must be a unix socket or tcp address:port like 127.0.0.1:6002")
	reflection = flag.Bool("reflect", false, "instead of running the service, provide a service definition to os.stdout using reflection")
}
