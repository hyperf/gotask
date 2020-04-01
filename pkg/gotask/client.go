package gotask

import (
	"github.com/fatih/pool"
	"github.com/pkg/errors"
	"github.com/spiral/goridge/v2"
	"net"
	"net/rpc"
	"strings"
)

type Pool struct {
	pool.Pool
}

var globalPool *Pool

//SetGo2PHPAddress sets the go2php server socket address
func SetGo2PHPAddress(address string) {
	*go2phpAddress = address
}

//GetGo2PHPAddress retrieves the go2php server socket address
func GetGo2PHPAddress() string {
	return *go2phpAddress
}

//NewAutoPool creates a connection pool using pre-defined addresses
func NewAutoPool() (*Pool, error) {
	addresses := strings.Split(*go2phpAddress, ",")
	return NewPool(addresses)
}

//NewPool creates a connection pool
func NewPool(addresses []string) (*Pool, error) {
	index := 0
	factory := func() (net.Conn, error) {
		return net.Dial(parseAddr(addresses[index%len(addresses)]))
	}
	p, err := pool.NewChannelPool(5, 30, factory)
	if err != nil {
		return nil, errors.Wrap(err, "Failed to create connection pool")
	}
	return &Pool{
		Pool: p,
	}, nil
}

// Client represents a client for go2php IPC.
type Client struct {
	*rpc.Client
}

// NewAutoClient creates a client connected to predefined connection pool.
func NewAutoClient() (c *Client, err error) {
	if globalPool == nil {
		globalPool, err = NewAutoPool()
		if err != nil {
			return nil, errors.Wrap(err, "Connection pool not available")
		}
	}
	conn, err := globalPool.Get()
	if err != nil {
		return nil, errors.Wrap(err, "Failed to get a connection from connection pool")
	}
	c = NewClient(conn)
	return c, nil
}

// NewClient returns a new Client using the connection provided.
func NewClient(conn net.Conn) *Client {
	return &Client{
		Client: rpc.NewClientWithCodec(goridge.NewClientCodec(conn)),
	}
}
