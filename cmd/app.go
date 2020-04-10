package main

import (
	"log"

	"github.com/hyperf/gotask/v2/pkg/gotask"
)

// App sample
type App struct{}

// Hi returns greeting message.
func (a *App) Hi(name interface{}, r *interface{}) error {
	*r = map[string]interface{}{
		"hello": name,
	}
	return nil
}

func main() {
	if err := gotask.Register(new(App)); err != nil {
		log.Fatalln(err)
	}
	if err := gotask.Run(); err != nil {
		log.Fatalln(err)
	}
}
