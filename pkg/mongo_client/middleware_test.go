package mongo_client

import (
	"encoding/json"
	"go.mongodb.org/mongo-driver/bson"
	"testing"
)

func TestBsonDeserialize(t *testing.T) {
	cases := []struct {
		name  string
		cases bson.D
	}{
		{"kv", bson.D{{"hello", "world"}}},
		{"number", bson.D{{"bar", 1}}},
		{"slice", bson.D{{"hello", []string{"value", "test"}}}},
		{"nil", bson.D{{"foo", nil}}},
		{"multiple", bson.D{
			{"hello", "world"},
			{"hello2", "world2"},
		}},
	}

	for _, c := range cases {
		c := c
		t.Run(c.name, func(t *testing.T) {
			t.Parallel()
			b, _ := bson.Marshal(c.cases)
			p := &bson.D{}
			m := BsonDeserialize(p)
			h := m(func(cmd interface{}, result *interface{}) error {
				a, _ := json.Marshal((*p)[0])
				b, _ := json.Marshal(c.cases[0])
				if string(a) != string(b) {
					t.Errorf("cmd is not equal, want %q, got %q", c.cases, cmd)
				}
				return nil
			})
			_ = h(b, nil)
		})
	}
}

func TestBsonSerialize(t *testing.T) {
	cases := []struct {
		name  string
		cases bson.D
	}{
		{"kv", bson.D{{"hello", "world"}}},
		{"number", bson.D{{"bar", 1}}},
		{"slice", bson.D{{"hello", []string{"value", "test"}}}},
		{"nil", bson.D{{"foo", nil}}},
		{"multiple", bson.D{
			{"hello", "world"},
			{"hello2", "world2"},
		}},
	}

	for _, c := range cases {
		c := c
		t.Run(c.name, func(t *testing.T) {
			t.Parallel()
			b, _ := bson.Marshal(c.cases)
			m := BsonSerialize()
			h := m(func(cmd interface{}, result *interface{}) error {
				*result = c.cases
				return nil
			})
			var result interface{}
			_ = h(nil, &result)
			b, ok := result.([]byte)
			if !ok {
				t.Errorf("result should be of type byte")
			}
			by, _ := bson.Marshal(c.cases)
			if string(by) != string(b) {
				t.Errorf("want %b, got %b", b, by)
			}

		})
	}
}
