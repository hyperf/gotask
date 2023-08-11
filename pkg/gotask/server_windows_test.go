//go:build windows
// +build windows

package gotask

import (
	"io/ioutil"
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

	if _, err := checkAddr("unix", dir+"/path/to/dir/temp.sock"); err != nil {
		t.Errorf("checkAddr should be able to create directory if not exist")
	}
}
