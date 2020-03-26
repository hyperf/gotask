package gotask

import (
	"flag"
	"fmt"
	"github.com/pkg/errors"
	"github.com/spiral/goridge"
	"log"
	"net"
	"net/rpc"
	"os"
	"os/signal"
	"strings"
	"syscall"
	"time"
)

var (
	Address *string
)

func init() {
	Address = flag.String("address", "127.0.0.1:6001", "must be a unix socket or tcp address:port like 127.0.0.1:6001")
	flag.Parse()
}

func checkProcess(pid int, quit chan bool) {
	process, err := os.FindProcess(int(pid))
	if err != nil {
		log.Printf("Failed to find process: %s\n", err)
		close(quit)
		return
	}
	err = process.Signal(syscall.Signal(0))
	if err != nil {
		log.Printf("process.Signal on pid %d returned: %v\n", pid, err)
		close(quit)
	}
}

func Register(receiver interface{}) error {
	return rpc.Register(receiver)
}

func Run() error {
	var (
		termChan  chan os.Signal
		ppid      int
		pdeadChan chan bool
		connChan  chan net.Conn
		ticker    *time.Ticker
		network   string
	)
	termChan = make(chan os.Signal)
	signal.Notify(termChan, os.Interrupt, os.Kill)
	ppid = os.Getppid()
	pdeadChan = make(chan bool)
	ticker = time.NewTicker(500 * time.Millisecond)
	connChan = make(chan net.Conn)

	if strings.Contains(*Address, ":") {
		network = "tcp"
	} else {
		network = "unix"
	}
	ln, err := net.Listen(network, *Address)
	if err != nil {
		return errors.Wrap(err, "Unable to listen")
	}
	defer ln.Close()

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

	go func() {
		for {
			conn, err := ln.Accept()
			if err != nil {
				log.Printf("on accept: %s\n", err.Error())
				continue
			}
			connChan <- conn
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
