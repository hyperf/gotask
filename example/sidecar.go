package main

import (
	"bytes"
	"encoding/base64"
	"fmt"
	"github.com/hyperf/gotask/v2/pkg/gotask"
	"io/ioutil"
	"log"
)

// App sample
type App struct{}

func (a *App) HelloString(name string, r *interface{}) error {
	*r = fmt.Sprintf("Hello, %s!", name)
	return nil
}

// Hello returns greeting message.
func (a *App) HelloInterface(name interface{}, r *interface{}) error {
	*r = map[string]interface{}{
		"hello": name,
	}
	return nil
}

type Name struct {
	Id        int    `json:"id"`
	FirstName string `json:"firstName"`
	LastName  string `json:"lastName"`
}

func (a *App) HelloStruct(name Name, r *interface{}) error {
	*r = map[string]Name{
		"hello": name,
	}
	return nil
}

func (a *App) HelloBytes(name []byte, r *[]byte) error {
	reader := base64.NewDecoder(base64.StdEncoding, bytes.NewReader(name))
	*r, _ = ioutil.ReadAll(reader)
	return nil
}

func (a *App) HelloError(name interface{}, r *interface{}) error {
	return fmt.Errorf("%s, it is possible to return error", name)
}

func (a *App) HelloPanic(name interface{}, r *interface{}) (e error) {
	defer func() {
		if p := recover(); p != nil {
			// Recovering from panic
			e = fmt.Errorf("panic in go: %v", p)
		}
	}()
	panic("Test if we can handle panic")
}

func main() {
	if err := gotask.Register(new(App)); err != nil {
		log.Fatalln(err)
	}
	if err := gotask.Run(); err != nil {
		log.Fatalln(err)
	}
}
