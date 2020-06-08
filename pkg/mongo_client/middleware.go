package mongo_client

import (
	"encoding/binary"
	"fmt"
	"github.com/hyperf/gotask/v2/pkg/gotask"
	"github.com/pkg/errors"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
)

// BsonDeserialize deserializes bson cmd into a struct cmd
func BsonDeserialize(ex interface{}) gotask.Middleware {
	return func(next gotask.Handler) gotask.Handler {
		return func(cmd interface{}, r *interface{}) error {
			b, ok := cmd.([]byte)
			if !ok {
				return fmt.Errorf("bsonDeserialize only accepts []byte")
			}
			e := bson.Unmarshal(b, ex)
			if e != nil {
				return errors.Wrap(e, "fails to unmarshal bson")
			}
			return next(ex, r)
		}
	}
}

// BsonSerialize serializes any result into a bson encoded result
func BsonSerialize() gotask.Middleware {
	return func(next gotask.Handler) gotask.Handler {
		return func(cmd interface{}, r *interface{}) (e error) {
			defer func() {
				if e != nil {
					*r = []byte{}
					return
				}
				if *r == nil {
					*r = []byte{}
					return
				}
				switch (*r).(type) {
				case int64:
					b := make([]byte, 8)
					binary.LittleEndian.PutUint64(b, uint64((*r).(int64)))
					*r = b
					return
				case string:
					*r = []byte((*r).(string))
					return
				default:
					_, *r, e = bson.MarshalValue(r)
					if e != nil {
						e = errors.Wrap(e, "unable to serialize bson")
					}
				}

			}()
			return next(cmd, r)
		}
	}
}

func ErrorFilter() gotask.Middleware {
	return func(next gotask.Handler) gotask.Handler {
		return func(cmd interface{}, r *interface{}) (e error) {
			defer func() {
				if e == mongo.ErrNilCursor || e == mongo.ErrNilDocument {
					e = nil
				}
				e = errors.Wrap(e, "error while executing mongo command")
			}()
			return next(cmd, r)
		}
	}
}

func stackMiddleware(ex interface{}) gotask.Middleware {
	return gotask.Chain(
		gotask.PanicRecover(),
		BsonDeserialize(ex),
		BsonSerialize(),
		ErrorFilter(),
	)
}
