package log

import (
	"testing"

	"github.com/hyperf/gotask/v2/pkg/gotask"
)

func testInfo(t *testing.T) {
	err := Info("hello", C{
		"Some": "Value",
	})
	if err != nil {
		t.Errorf("level Info log should be successful, got %+v", err)
	}
}

func TestAll(t *testing.T) {
	t.Parallel()
	gotask.SetGo2PHPAddress("../../tests/test.sock")
	for i := 0; i < 50; i++ {
		t.Run("testAll", func(t *testing.T) {
			t.Parallel()
			testInfo(t)
		})
	}
}
