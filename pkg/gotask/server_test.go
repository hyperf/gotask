package gotask

import (
	"log"
	"os"
	"testing"
)

func TestClearAddr(t *testing.T) {
	if err := clearAddr("unix", "/tmp/non-exist.sock"); err != nil {
		t.Errorf("clearAddr should not return error for non-exist files")
	}
	if err := clearAddr("tcp", "127.0.0.1:6000"); err != nil {
		t.Errorf("clearAddr should not return error for tcp ports")
	}
	file, err := os.Create("/tmp/temp.sock")
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()
	if err := clearAddr("unix", "/tmp/temp.sock"); err != nil {
		t.Errorf("clearAddr should be able to clear unix socket")
	}
	_, err = os.Stat("/tmp/temp.sock")
	if !os.IsNotExist(err) {
		t.Errorf("unix socket are not cleared, %v", err)
	}
}
