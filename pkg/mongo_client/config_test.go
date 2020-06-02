package mongo_client

import (
	"os"
	"testing"
	"time"
)

func TestGetTimeout(t *testing.T) {
	_ = os.Setenv("SOMEENV", "20s")

	someEnv := getTimeout("SOMEENV", time.Second)
	if someEnv != 20 * time.Second {
		t.Errorf("want %s, got %s", "20s", someEnv)
	}

	otherEnv := getTimeout("NONEXIST", time.Second)
	if otherEnv != time.Second {
		t.Errorf("want %s, got %s", "20s", otherEnv)
	}
}
