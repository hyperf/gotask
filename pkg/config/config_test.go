package config

import (
	"testing"

	"github.com/hyperf/gotask/v2/pkg/gotask"
)

func testGet(t *testing.T) {
	testing.Init()
	enable, err := Get("gotask.php2go.enable", true)
	if err != nil {
		t.Errorf("Get returns err %e", err)
	}
	if enable != true {
		t.Errorf("enable should be true")
	}
}

func testSet(t *testing.T) {
	testing.Init()
	err := Set("gotask.non_exist", []string{"some", "value"})
	if err != nil {
		t.Errorf("Set returns err %+v", err)
	}
	val, err := Get("gotask.non_exist", []string{"other", "value"})
	if err != nil {
		t.Errorf("Get returns err %+v", err)
	}
	if _, ok := val.([]interface{}); !ok {
		t.Errorf("val should be slice, but got %+v", val)
	}
	s, _ := val.([]interface{})
	if s[0] != "some" {
		t.Errorf("key 1 should be some, but got %s", s[0])
	}
	if s[1] != "value" {
		t.Errorf("key 2 should be value, but got %s", s[1])
	}
}

func testHas(t *testing.T) {
	testing.Init()
	has, err := Has("gotask.socket_address")
	if err != nil {
		t.Errorf("Set returns err %e", err)
	}
	if has != true {
		t.Errorf("expect true, got %v", has)
	}
	val, err := Has("gotask.no_no")
	if val == true {
		t.Errorf("expect false, got %v", has)
	}
}

func TestAll(t *testing.T) {
	gotask.SetGo2PHPAddress("../../tests/test.sock")
	for i := 0; i < 50; i++ {
		t.Run("testAll", func(t *testing.T) {
			t.Parallel()
			testHas(t)
			testGet(t)
			testSet(t)
		})
	}
}
