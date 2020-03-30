package gotask

import (
	"github.com/fatih/pool"
	"github.com/pkg/errors"
	"github.com/spiral/goridge"
	"net"
	"net/rpc"
)

type Pool struct {
	pool.Pool
}

func NewPool() (*Pool, error) {
	factory := func() (net.Conn, error) { return net.Dial(parseAddr(*go2phpAddress)) }
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

func NewClient(conn net.Conn) *Client {
	return &Client{
		Client: rpc.NewClientWithCodec(goridge.NewClientCodec(conn)),
	}
}
