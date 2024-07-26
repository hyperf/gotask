//go:build !windows
// +build !windows

package gotask

import(
	"os"
	"syscall"

	"github.com/pkg/errors"
)

func getStat(path string) (isWritable bool, err error) {
	var stat syscall.Stat_t
	if err = syscall.Stat(path, &stat); err != nil {
		return false, err
	}

	err = nil
	if uint32(os.Geteuid()) != stat.Uid {
		return false, errors.Errorf("user doesn't have permission to write to %s", path)
	}

	return true, nil
}
