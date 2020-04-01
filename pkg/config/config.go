package config

import (
	"github.com/reasno/gotask/pkg/gotask"
)

const phpGet = "Reasno\\GoTask\\Wrapper\\ConfigWrapper::get"
const phpSet = "Reasno\\GoTask\\Wrapper\\ConfigWrapper::set"
const phpHas = "Reasno\\GoTask\\Wrapper\\ConfigWrapper::has"

func Get(key string, fallback interface{}) (value interface{}, err error) {
	client, err := gotask.NewAutoClient()
	if err != nil {
		return nil, err
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
