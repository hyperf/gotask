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

func NewPool() (*Pool, error) {
	index := 0
	factory := func() (net.Conn, error) {
		addresses := strings.Split(*go2phpAddress, ",")
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

func NewClient(conn net.Conn) *Client {
	return &Client{
		Client: rpc.NewClientWithCodec(goridge.NewClientCodec(conn)),
	}
}
