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

func SetGo2PHPAddress(address string) {
	*go2phpAddress = address
}

func GetGo2PHPAddress() string {
	return *go2phpAddress
}

func NewAutoPool() (*Pool, error) {
	addresses := strings.Split(*go2phpAddress, ",")
	return NewPool(addresses)
}

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

type Client struct {
	*rpc.Client
}

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

func NewClient(conn net.Conn) *Client {
	return &Client{
		Client: rpc.NewClientWithCodec(goridge.NewClientCodec(conn)),
	}
}
