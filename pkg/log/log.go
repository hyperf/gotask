package log

import (
	"github.com/reasno/gotask/pkg/gotask"
)

const phpLog = "Reasno\\GoTask\\Wrapper\\LoggerWrapper::log"

func Emergency(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "emergency",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Alert(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "alert",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Critical(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "critical",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Error(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "error",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Warning(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "warning",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Notice(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "notice",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Info(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "info",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Debug(message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   "debug",
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}

func Log(level string, message string, context map[string]interface{}) error {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return err
	}
	payload := map[string]interface{}{
		"level":   level,
		"context": context,
		"message": message,
	}
	err = client.Call(phpLog, payload, nil)
	return err
}
