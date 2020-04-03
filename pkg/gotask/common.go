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
		return "bool"
	}
	return ""
}

// Reflect if an interface is either a struct or a pointer to a struct
// and has the defined member field, if error is nil, the given
// FieldName exists and is accessible with reflect.
func property(i interface{}, fieldName string, fallback string) string {
	ValueIface := reflect.ValueOf(i)

	// Check if the passed interface is a pointer
	if ValueIface.Type().Kind() != reflect.Ptr {
		// Create a new type of Iface's Type, so we have a pointer to work with
		ValueIface = reflect.New(reflect.TypeOf(i))
	}

	// 'dereference' with Elem() and get the field by name
	Field := ValueIface.Elem().FieldByName(fieldName)
	if !Field.IsValid() || !(Field.Kind() == reflect.String) {
		return fallback
	}
	return Field.String()
}
