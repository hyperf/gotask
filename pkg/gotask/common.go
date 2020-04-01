package gotask

import (
	"reflect"
	"strings"
)

type Function struct {
	Name           string
	Raw            bool
	ParamModifier  string
	ResultModifier string
}
type Class struct {
	Name      string
	Functions []Function
}

func parseAddr(addr string) (string, string) {
	var network string
	if strings.Contains(addr, ":") {
		network = "tcp"
	} else {
		network = "unix"
	}
	return network, addr
}

func reflectStruct(i interface{}) *Class {
	var val reflect.Type
	if reflect.TypeOf(i).Kind() != reflect.Ptr {
		val = reflect.PtrTo(reflect.TypeOf(i))
	} else {
		val = reflect.TypeOf(i)
	}
	functions := make([]Function, 0)
	for i := 0; i < val.NumMethod(); i++ {
		f := Function{
			Name:           val.Method(i).Name,
			Raw:            val.Method(i).Type.In(1) == reflect.TypeOf([]byte{}),
			ParamModifier:  getModifier(val.Method(i).Type.In(1)),
			ResultModifier: getModifier(val.Method(i).Type.In(2).Elem()),
		}
		functions = append(functions, f)
	}
	return &Class{
		Name:      val.Elem().Name(),
		Functions: functions,
	}
}

func getModifier(t reflect.Type) string {
	if t == reflect.TypeOf([]byte{}) {
		return "string"
	}
	if t.Kind() == reflect.Int {
		return "int"
	}
	if t.Kind() == reflect.Float64 {
		return "float"
	}
	if t.Kind() == reflect.Float32 {
		return "float"
	}
	if t.Kind() == reflect.Bool {
		return "boolean"
	}
	return ""
}
