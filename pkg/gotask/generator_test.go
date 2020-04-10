package gotask

import (
	"testing"
)

func TestGenerator(t *testing.T) {
	expected := `<?php

declare(strict_types=1);

namespace App\GoTask;

use Hyperf\GoTask\GoTask;
use Hyperf\GoTask\GoTaskProxy;

class Worker extends GoTaskProxy
{
    /**
     * @param string $payload
     * @return bool
     */
    public function test(string $payload) : bool
    {
        return (bool)parent::call("Worker.Test", $payload, 0);
    }

    /**
     * @param  $payload
     * @return mixed
     */
    public function debug($payload)
    {
        return parent::call("Worker.Debug", $payload, GoTask::PAYLOAD_RAW);
    }
}`
	namespace := "App\\GoTask"
	class := Class{
		Name: "Worker",
		Functions: []Function{
			Function{
				Name:           "Test",
				Raw:            false,
				ParamModifier:  "string",
				ResultModifier: "bool",
			},
			Function{
				Name:           "Debug",
				Raw:            true,
				ParamModifier:  "",
				ResultModifier: "",
			},
		},
	}
	if body(&namespace, &class) != expected {
		t.Errorf("expecting %s, got %s", expected, body(&namespace, &class))
	}
}
