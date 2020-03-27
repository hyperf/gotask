package gotask

import (
	"flag"
	"fmt"
	"github.com/pkg/errors"
	"github.com/spiral/goridge/v2"
	"net"
	"net/rpc"
	"os"
	"os/signal"
	"strings"
	"syscall"
	"time"
)

var (
	address    *string
	standalone *bool
)

func init() {
	standalone = flag.Bool("standalone", false, "ignore parent process status")
	address = flag.String("address", "", "must be a unix socket or tcp address:port like 127.0.0.1:6001")
	flag.Parse()
}

func checkProcess(pid int, quit chan bool) {
	if *standalone {
		return
	}
	process, err := os.FindProcess(int(pid))
	if err != nil {
		close(quit)
		return
	}
	err = process.Signal(syscall.Signal(0))
	if err != nil {
		close(quit)
	}
}

func listen(connChan chan net.Conn) (closer func() error, err error) {
	var network string
	if strings.Contains(*address, ":") {
		network = "tcp"
	} else {
		network = "unix"
	}
	ln, err := net.Listen(network, *address)
	if err != nil {
		return nil, errors.Wrap(err, "Unable to listen")
	}
	go func() {
		for {
			conn, err := ln.Accept()
			if err != nil {
				continue
			}
			connChan <- conn
		}
	}()
	return ln.Close, nil
}

func Register(receiver interface{}) error {
	return rpc.Register(receiver)
}

func SetAddress(addr string) {
	*address = addr
}

func GetAddress() string {
	return *address
}

func Run() error {
	var (
		termChan  chan os.Signal
		ppid      int
		pdeadChan chan bool
		connChan  chan net.Conn
		ticker    *time.Ticker
	)
	termChan = make(chan os.Signal)
	signal.Notify(termChan, os.Interrupt, os.Kill)
	ppid = os.Getppid()
	pdeadChan = make(chan bool)
	ticker = time.NewTicker(500 * time.Millisecond)
	connChan = make(chan net.Conn)

	if *address == "" {
		relay := goridge.NewPipeRelay(os.Stdin, os.Stdout)
		codec := goridge.NewCodecWithRelay(relay)
		go rpc.ServeCodec(codec)
	} else {
		closer, err := listen(connChan)
		if err != nil {
			return err
		}
		defer closer()
	}

	go func() {
		for {
			select {
			case <-pdeadChan:
				return
			case <-ticker.C:
				checkProcess(ppid, pdeadChan)
			}
		}
	}()

	for {
		select {
		case sig := <-termChan:
			return fmt.Errorf("received system call:%+v, shutting down\n", sig)
		case <-pdeadChan:
			return fmt.Errorf("parent process dead\n")
		case conn := <-connChan:
			go rpc.ServeCodec(goridge.NewCodec(conn))
		}
	}
}
