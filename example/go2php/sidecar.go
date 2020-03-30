package main

import (
	"fmt"
	"github.com/reasno/gotask/pkg/gotask"
	"log"
)

func main() {
	pool, err := gotask.NewPool()
	if err != nil {
		log.Fatalln(err)
	}
	conn, err := pool.Get()
	if err != nil {
		log.Fatalln(err)
	}
	client := gotask.NewClient(conn)
	defer client.Close()

	{
		var res []byte
		err = client.Call("Example::echo", "Reasno", &res)
		if err != nil {
			log.Fatalln(err)
		}
		fmt.Println(string(res))
	}

	{
		var res []byte
		err = client.Call("Example::HelloString", "Reasno", &res)
		if err != nil {
			log.Fatalln(err)
		}
		fmt.Println(string(res))
	}

	{
		type p map[string]interface{}
		var res struct {
			Hello interface{} `json:"hello"`
		}
		err = client.Call("Example::HelloStruct", p{"Name": "Reasno"}, &res)
		if err != nil {
			log.Fatalln(err)
		}
		fmt.Printf("%+v\n", res)
	}

	{
		var res interface{}
		err = client.Call("Example::HelloError", "Reasno", &res)
		if err != nil {
			log.Fatalln(err)
		}
	}
}
