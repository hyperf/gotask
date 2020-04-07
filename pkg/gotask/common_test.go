package gotask

import (
	"testing"
)

type Mock struct{}

func (m Mock) MockMethod(arg interface{}, r *interface{}) error {
	return nil
}
func (m Mock) MockMethodBytes(arg []byte, r *interface{}) error {
	return nil
}
func (m *Mock) Pointer(arg []byte, r *interface{}) error {
	return nil
}
func TestReflectStruct(t *testing.T) {
	m := Mock{}
	out := reflectStruct(m)
	if out.Name != "Mock" {
		t.Errorf("Name must be Mock, got %s", out.Name)
	}
	if out.Functions[0].Name != "MockMethod" {
		t.Errorf("Name must be MockMethod, got %s", out.Functions[0].Name)
	}
	if out.Functions[0].Raw != false {
		t.Errorf("Raw must be false, got %+v", out.Functions[0].Raw)
	}
	if out.Functions[1].Name != "MockMethodBytes" {
		t.Errorf("Name must be MockMethodBytes, got %s", out.Functions[1].Name)
	}
	if out.Functions[1].Raw != true {
		t.Errorf("Name must be true, got %+v", out.Functions[1].Raw)
	}
	if out.Functions[2].Name != "Pointer" {
		t.Errorf("Name must be MockMethodBytes, got %s", out.Functions[2].Name)
	}
	if out.Functions[2].Raw != true {
		t.Errorf("Name must be true, got %+v", out.Functions[2].Raw)
	}
}
