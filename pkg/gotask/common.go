package gotask

import "strings"

func parseAddr(addr string) (string, string) {
	var network string
	if strings.Contains(addr, ":") {
		network = "tcp"
	} else {
		network = "unix"
	}
	return network, addr
}
