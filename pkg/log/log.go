package log

import (
	"github.com/hyperf/gotask/v2/pkg/gotask"
)

const phpLog = "Hyperf\\GoTask\\Wrapper\\LoggerWrapper::log"

// C is a type for passing PSR context.
type C map[string]interface{}

// Map returns itself
func (c C) Map() map[string]interface{} {
	return c
}

// Mapper is an interface for PSR Context
type Mapper interface {
	Map() map[string]interface{}
}

// Emergency logger
func Emergency(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "emergency",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Alert logger
func Alert(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "alert",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Critical logger
func Critical(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "critical",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Error logger
func Error(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "error",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Warning logger
func Warning(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "warning",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Notice logger
func Notice(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "notice",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Info logger
func Info(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "info",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Debug logger
func Debug(message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "debug",
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

// Log logs a message at any given level
func Log(level string, message string, context Mapper) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   level,
		"context": context.Map(),
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}
