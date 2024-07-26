//go:build windows
// +build windows

package gotask

func getStat(path string) (isWritable bool, err error) {
	return true, nil
}
