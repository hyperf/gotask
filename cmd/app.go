package main

import (
	"github.com/reasno/gotask/pkg/gotask"
)
// App sample
type App struct{}

// Hi returns greeting message.
func (a *App) Hi(name interface{}, r *interface{}) error {
	*r = map[string]interface{}{
		"hello": name,
	}
	//also try: *r = fmt.Sprintf("Hello, %s!", name)
	return nil
}

func main() {
	gotask.Register(new(App))
	gotask.Run()
}

