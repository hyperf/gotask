package config

import (
	"fmt"

	"github.com/hyperf/gotask/v2/pkg/gotask"
)

const phpGet = "Hyperf\\GoTask\\Wrapper\\ConfigWrapper::get"
const phpSet = "Hyperf\\GoTask\\Wrapper\\ConfigWrapper::set"
const phpHas = "Hyperf\\GoTask\\Wrapper\\ConfigWrapper::has"

// Get retrieves a configuration from PHP, and fallback to the second parameter
// if a config is missing at PHP's end.
func Get(key string, fallback interface{}) (value interface{}, err error) {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return fallback, err
	}
	err = client.Call(phpGet, key, &value)
	if err != nil {
		return value, err
	}
	if value == nil {
		return fallback, nil
	}
	return value, nil
}

// GetString returns a string config
func GetString(key string, fallback string) (value string, err error) {
	untyped, err := Get(key, fallback)
	if err != nil {
		return fallback, err
	}
	if typed, ok := untyped.(string); ok {
		return typed, nil
	}
	return fallback, fmt.Errorf("config %s expected to be string, got %+v instead", key, untyped)
}

// GetInt returns a int config
func GetInt(key string, fallback int) (value int, err error) {
	untyped, err := Get(key, fallback)
	if err != nil {
		return fallback, err
	}
	if typed, ok := untyped.(int); ok {
		return typed, nil
	}
	return fallback, fmt.Errorf("config %s expected to be int, got %+v instead", key, untyped)
}

// GetFloat returns a float64 config
func GetFloat(key string, fallback float64) (value float64, err error) {
	untyped, err := Get(key, fallback)
	if err != nil {
		return fallback, err
	}
	if typed, ok := untyped.(float64); ok {
		return typed, nil
	}
	return fallback, fmt.Errorf("config %s expected to be float64, got %+v instead", key, untyped)
}

// GetBool returns a boolean config
func GetBool(key string, fallback bool) (value bool, err error) {
	untyped, err := Get(key, fallback)
	if err != nil {
		return fallback, err
	}
	if typed, ok := untyped.(bool); ok {
		return typed, nil
	}
	return fallback, fmt.Errorf("config %s expected to be bool, got %+v instead", key, untyped)
}

// Has checks if a configuration exists in PHP
func Has(key string) (value bool, err error) {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return false, err
	}
	err = client.Call(phpHas, key, &value)
	if err != nil {
		return value, err
	}
	return value, nil
}

// Set sets a configuration in PHP
func Set(key string, val interface{}) (err error) {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return nil
	}
	payload := map[string]interface{}{
		"key":   key,
		"value": val,
	}
	err = client.Call(phpSet, payload, nil)
	if err != nil {
		return err
	}
	return nil
}
