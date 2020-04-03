package gotask

import (
	"bytes"
	"github.com/pkg/errors"
	"io/ioutil"
	"os"
	"path/filepath"
	"text/template"
	"unicode"
)

const phpBody = `<?php

declare(strict_types=1);
{{ $ns := .Namespace -}}
{{if $ns}}
namespace {{ $ns }};
{{end}}
use Reasno\GoTask\GoTaskProxy;
{{ $class := .Class.Name }}
class {{ $class }} extends GoTaskProxy
{
{{- range $m := .Class.Functions}}
    /**
     * @param {{ $m.ParamModifier }} $payload
     * @return {{ $m.ResultModifier | returnAnnotation }}
     */
    public function {{ $m.Name | lcfirst }}({{ $m.ParamModifier | postSpace }}$payload){{ $m.ResultModifier | returnTypeHint }}
    {
        return parent::call({{ methodName $class $m.Name }}, $payload, {{ $m.Raw | flag}});
    }
{{end -}}
}`

var tpl *template.Template

func init() {
	tpl = template.Must(template.New("phpBody").Funcs(template.FuncMap{
		"flag": func(raw *bool) string {
			if *raw {
				return "GoTask::PAYLOAD_RAW"
			}
			return "0"
		},
		"returnAnnotation": func(modifier *string) string {
			if *modifier != "" {
				return *modifier
			}
			return "mixed"
		},
		"returnTypeHint": func(modifier *string) string {
			if *modifier != "" {
				return " : " + *modifier
			}
			return ""
		},
		"postSpace": func(name *string) string {
			if *name == "" {
				return ""
			}
			return *name + " "
		},
		"lcfirst": func(name *string) string {
			for _, v := range *name {
				u := string(unicode.ToLower(v))
				return u + (*name)[len(u):]
			}
			return ""
		},
		"methodName": func(class string, method string) string {
			return "\"" + class + "." + method + "\""
		},
	}).Parse(phpBody))
}

// generate php file body
func body(namespace *string, class *Class) string {
	out := bytes.NewBuffer(nil)

	data := struct {
		Namespace string
		Class     Class
	}{
		Namespace: *namespace,
		Class:     *class,
	}

	err := tpl.Execute(out, data)
	if err != nil {
		panic(err)
	}

	return out.String()
}

func generatePHP(receiver interface{}) error {
	namespace := property(receiver, "Namespace", "App\\GoTask")
	class := reflectStruct(receiver)
	dirPath := property(receiver, "Path", "./../app/GoTask")
	err := os.MkdirAll(dirPath, os.FileMode(0755))
	if err != nil {
		return errors.Wrap(err, "cannot create dir for php files")
	}
	fullPath, err := filepath.Abs(filepath.Clean(dirPath) + "/" + class.Name + ".php")
	if err != nil {
		return errors.Wrap(err, "invalid file path")
	}
	out := body(&namespace, class)
	err = ioutil.WriteFile(fullPath, []byte(out), os.FileMode(0755))
	if err != nil {
		return errors.Wrap(err, "failed to generate PHP file")
	}
	return nil
}
