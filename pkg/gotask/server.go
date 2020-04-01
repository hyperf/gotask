package gotask

import (
	"context"
	"fmt"
	jsoniter "github.com/json-iterator/go"
	"github.com/oklog/run"
	"github.com/pkg/errors"
	"github.com/spiral/goridge/v2"
	"log"
	"net"
	"net/rpc"
	"os"
	"os/signal"
	"syscall"
	"time"
)

var g run.Group

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

// Register a net/rpc compatible service
func Register(receiver interface{}) error {
	if !*reflection {
		return rpc.Register(receiver)
	}
	out := reflectStruct(receiver)
	s, err := jsoniter.Marshal(out)
	if err != nil {
		return errors.Wrap(err, "unable to create reflection")
	}
	log.Println(string(s))
	return nil
}

// Set the address of socket
func SetAddress(addr string) {
	*address = addr
}

// Get the address of the socket
func GetAddress() string {
	return *address
}

// Run the sidecar, receive any fatal errors.
func Run() error {
	if *reflection {
		return nil
	}

	if *listenOnPipe {
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
		network, addr := parseAddr(*address)
		ln, err := net.Listen(network, addr)
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

// Add an actor (function) to the group. Each actor must be pre-emptable by an
// interrupt function. That is, if interrupt is invoked, execute should return.
// Also, it must be safe to call interrupt even after execute has returned.
//
// The first actor (function) to return interrupts all running actors.
// The error is passed to the interrupt functions, and is returned by Run.
func Add(execute func() error, interrupt func(error)) {
	g.Add(execute, interrupt)
}
