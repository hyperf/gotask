package gotask

import (
	"context"
	"flag"
	"fmt"
	"github.com/oklog/run"
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
	var g run.Group
	if *address == "" {
		relay := goridge.NewPipeRelay(os.Stdin, os.Stdout)
		codec := goridge.NewCodecWithRelay(relay)
		g.Add(func() error {
			rpc.ServeCodec(codec)
			return fmt.Errorf("pipe is closed")
		}, func(err error) {
			_ = os.Stdin.Close()
			_ = os.Stdout.Close()
			_ = codec.Close()
		})
	}

	if *address != "" {
		var network string
		if strings.Contains(*address, ":") {
			network = "tcp"
		} else {
			network = "unix"
		}
		ln, err := net.Listen(network, *address)
		if err != nil {
			return errors.Wrap(err, "Unable to listen")
		}
		g.Add(func() error {
			for {
				conn, err := ln.Accept()
				if err != nil {
					return err
				}
				go rpc.ServeCodec(goridge.NewCodec(conn))
			}
		}, func(err error) {
			_ = ln.Close()
		})
	}

	{
		var (
			termChan  chan os.Signal
			ppid      int
			pdeadChan chan bool
			ticker    *time.Ticker
		)
		termChan = make(chan os.Signal)
		signal.Notify(termChan, os.Interrupt, os.Kill)
		ppid = os.Getppid()
		pdeadChan = make(chan bool)
		ticker = time.NewTicker(500 * time.Millisecond)
		ctx, cancel := context.WithCancel(context.Background())
		g.Add(func() error {
			for {
				select {
				case sig := <-termChan:
					return fmt.Errorf("received system call:%+v, shutting down\n", sig)
				case <-pdeadChan:
					return fmt.Errorf("parent process dead")
				case <-ticker.C:
					checkProcess(ppid, pdeadChan)
				case <-ctx.Done():
					return ctx.Err()
				}
			}
		}, func(err error) {
			cancel()
		})
	}

	return g.Run()
}
