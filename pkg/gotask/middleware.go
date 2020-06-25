package gotask

import (
	"fmt"
)

type Handler func(cmd interface{}, result *interface{}) error

type Middleware func(next Handler) Handler

func Chain(outer Middleware, others ...Middleware) Middleware {
	return func(next Handler) Handler {
		for i := len(others) - 1; i >= 0; i-- { // reverse
			next = others[i](next)
		}
		return outer(next)
	}
}

func PanicRecover() Middleware {
	return func(next Handler) Handler {
		return func(cmd interface{}, r *interface{}) (e error) {
			defer func() {
				if rec := recover(); rec != nil {
					e = fmt.Errorf("panic: %s", rec)
				}
			}()
			return next(cmd, r)
		}
	}
}
