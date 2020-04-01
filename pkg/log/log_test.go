package log

import (
	"github.com/reasno/gotask/pkg/gotask"
	"testing"
)

func testInfo(t *testing.T) {
	err := Info("hello", map[string]interface{}{
		"Some": "value",
	})
	if err != nil {
		t.Errorf("level Info log should be successful, got %e", err)
	}
}

func TestAll(t *testing.T) {
	gotask.SetGo2PHPAddress("/tmp/test.sock")
	for i := 0; i < 50; i++ {
		t.Run("testAll", func(t *testing.T) {
			t.Parallel()
			testInfo(t)
		})
	}
}
