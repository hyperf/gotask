package mongo_client

import (
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

func parseModels(arg []map[string][]bson.Raw) []mongo.WriteModel {
	var models = make([]mongo.WriteModel, 0, len(arg))
	for _, v := range arg {
		for kk, vv := range v {
			models = append(models, makeModel(kk, vv))
		}
	}
	return models
}

func makeModel(k string, v []bson.Raw) mongo.WriteModel {
	switch k {
	case "insertOne":
		m := mongo.NewInsertOneModel()
		if len(v) == 0 {
			return m
		}
		m.SetDocument(v[0])
		return m
	case "updateOne":
		m := mongo.NewUpdateOneModel()
		if len(v) == 0 {
			return m
		}
		m.SetFilter(v[0])
		if len(v) == 1 {
			return m
		}
		m.SetUpdate(v[1])
		if len(v) == 2 {
			return m
		}
		o := getOptions(v[2])
		m.SetUpsert(o.Upsert)
		if o.Collation == nil {
			return m
		}
		m.SetCollation(o.Collation)
		return m
	case "updateMany":
		m := mongo.NewUpdateManyModel()
		if len(v) == 0 {
			return m
		}
		m.SetFilter(v[0])
		if len(v) == 1 {
			return m
		}
		m.SetUpdate(v[1])
		if len(v) == 2 {
			return m
		}
		o := getOptions(v[2])
		m.SetUpsert(o.Upsert)
		if o.Collation == nil {
			return m
		}
		m.SetCollation(o.Collation)
		return m
	case "replaceOne":
		m := mongo.NewReplaceOneModel()
		if len(v) == 0 {
			return m
		}
		m.SetFilter(v[0])
		if len(v) == 1 {
			return m
		}
		m.SetReplacement(v[1])
		if len(v) == 2 {
			return m
		}
		o := getOptions(v[2])
		m.SetUpsert(o.Upsert)
		if o.Collation == nil {
			return m
		}
		m.SetCollation(o.Collation)
		return m
	case "deleteOne":
		m := mongo.NewDeleteOneModel()
		if len(v) == 0 {
			return m
		}
		m.SetFilter(v[0])
		if len(v) == 1 {
			return m
		}
		o := getOptions(v[1])
		if o.Collation == nil {
			return m
		}
		m.SetCollation(o.Collation)
		return m
	case "deleteMany":
		m := mongo.NewDeleteManyModel()
		if len(v) == 0 {
			return m
		}
		m.SetFilter(v[0])
		if len(v) == 1 {
			return m
		}
		o := getOptions(v[1])
		if o.Collation == nil {
			return m
		}
		m.SetCollation(o.Collation)
		return m
	default:
		return nil
	}
}

type option struct {
	Collation    *options.Collation   `bson:"collation"`
	Upsert       bool                 `bson:"upsert"`
	ArrayFilters options.ArrayFilters `bson:"arrayFilters"`
}

func getOptions(v bson.Raw) *option {
	var o option
	err := bson.Unmarshal(v, &o)
	if err != nil {
		panic(err)
	}
	return &o
}
