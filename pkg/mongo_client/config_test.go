package mongo_client

import (
	"os"
	"testing"
	"time"
)

func TestGetTimeout(t *testing.T) {
	t.Parallel()
	_ = os.Setenv("SOMEENV", "20s")

	cases := [][]interface{}{
		{"SOMEENV", 20 * time.Second},
		{"NONEXIST", time.Second},
	}

	for _, tt := range cases {
		tt := tt
		t.Run(tt[0].(string), func(t *testing.T) {
			t.Parallel()
			s := getTimeout(tt[0].(string), time.Second)
			if s != tt[1] {
				t.Errorf("got %q, want %q", s, tt[1])
			}
		})
	}
}
