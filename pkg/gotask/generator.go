package gotask

import (
	"bytes"
	"fmt"
	"io/ioutil"
	"os"
	"path/filepath"
	"text/template"
	"unicode"

	"github.com/pkg/errors"
)

const phpBody = `<?php

declare(strict_types=1);
{{ $ns := .Namespace -}}
{{if $ns}}
namespace {{ $ns }};
{{end}}
use Hyperf\GoTask\GoTask;
use Hyperf\GoTask\GoTaskProxy;
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
        return {{ $m.ResultModifier | cast }}parent::call({{ methodName $class $m.Name }}, $payload, {{ $m.Raw | flag}});
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
		"cast": func(modifier *string) string {
			if *modifier != "" {
				return "(" + *modifier + ")"
			}
			return ""
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
	namespace := property(receiver, "PHPNamespace", "App\\GoTask")
	class := reflectStruct(receiver)
	out := body(&namespace, class)
	dirPath := property(receiver, "PHPPath", "")
	if dirPath != "" {
		err := os.MkdirAll(dirPath, os.FileMode(0755))
		if err != nil {
			return errors.Wrap(err, "cannot create dir for php files")
		}
		fullPath, err := filepath.Abs(filepath.Clean(dirPath) + "/" + class.Name + ".php")
		if err != nil {
			return errors.Wrap(err, "invalid file path")
		}

		err = ioutil.WriteFile(fullPath, []byte(out), os.FileMode(0755))
		if err != nil {
			return errors.Wrap(err, "failed to generate PHP file")
		}
	} else {
		fmt.Print(out)
	}
	return nil
}
