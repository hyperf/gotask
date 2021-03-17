package gotask

import (
	"io/ioutil"
	"log"
	"os"
	"testing"
)

func TestClearAddr(t *testing.T) {
	dir, _ := ioutil.TempDir("", "")
	defer os.Remove(dir)

	if _, err := checkAddr("unix", dir+"/non-exist.sock"); err != nil {
		t.Errorf("checkAddr should not return error for non-exist files")
	}
	if _, err := checkAddr("tcp", "127.0.0.1:6000"); err != nil {
		t.Errorf("checkAddr should not return error for tcp ports")
	}
	file, err := os.Create("/tmp/temp.sock")
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()
	if _, err := checkAddr("unix", "/tmp/temp.sock"); err != nil {
		t.Errorf("checkAddr should be able to clear unix socket")
	}
	_, err = os.Stat("/tmp/temp.sock")
	if !os.IsNotExist(err) {
		t.Errorf("unix socket are not cleared, %v", err)
	}

	if _, err := checkAddr("unix", dir+"/path/to/dir/temp.sock"); err != nil {
		t.Errorf("checkAddr should be able to create directory if not exist")
	}

	if _, err := checkAddr("unix", "/private/temp.sock"); err == nil {
		t.Error("unix socket shouldn't be created")
	}
}
