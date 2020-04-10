package main

import (
	"encoding/base64"
	"fmt"
	"log"

	"github.com/hyperf/gotask/v2/pkg/gotask"
)

func main() {
	client, err := gotask.NewAutoClient()
	if err != nil {
		log.Fatalln(err)
	}
	defer client.Close()

	{
		var res []byte
		err = client.Call("Example::HelloString", "Hyperf", &res)
		if err != nil {
			log.Fatalln(err)
		}
		fmt.Println(string(res))
	}

	{
		var p interface{}
		p = []string{"jack", "jill"}
		var res interface{}
		err = client.Call("Example::HelloInterface", p, &res)
		if err != nil {
			log.Fatalln(err)
		}
		fmt.Printf("%+v\n", res)
	}

	{
		type Name struct {
			Id        int    `json:"id"`
			FirstName string `json:"firstName"`
			LastName  string `json:"lastName"`
		}
		var res struct {
			Hello interface{} `json:"hello"`
		}
		err = client.Call("Example::HelloStruct", Name{Id: 23, FirstName: "LeBron", LastName: "James"}, &res)
		if err != nil {
			log.Fatalln(err)
		}
		fmt.Printf("%+v\n", res)
	}

	{
		var p []byte
		var res []byte
		p = make([]byte, 100)
		base64.StdEncoding.Encode(p, []byte("My Bytes"))
		err = client.Call("Example::HelloBytes", p, &res)
		if err != nil {
			log.Fatalln(err)
		}
		fmt.Printf("%+v\n", string(res))
	}

	{
		var res interface{}
		err = client.Call("Example::HelloError", "Hyperf", &res)
		if err != nil {
			log.Fatalln(err)
		}
	}
}
