# GoTask

English | [中文](./README-CN.md)
 
[![Build Status](https://travis-ci.org/hyperf/gotask.svg?branch=master)](https://travis-ci.org/hyperf/gotask)

GoTask spawns a go process as a Swoole sidecar and establishes a bi-directional IPC to offload heavy-duties to Go. Think of it as a Swoole Taskworker in Go.

```bash
composer require hyperf/gotask
```

## Feature

* [High performance with low footprint.](https://github.com/reasno/gotask-benchmark)
* Based on Swoole 4 coroutine socket API.
* Support Unix Socket, TCP and stdin/stdout pipes.
* Support both PHP-to-Go and Go-to-PHP calls.
* Automatic sidecar lifecycle management.
* Correctly handle remote error.
* Support both structural payload and binary payload.
* Sidecar API compatible with net/rpc.
* Baked-in connection pool.
* Optionally integrated with Hyperf framework.

## Perfect For
* Blocking operations in Swoole, such as MongoDB queries.
* CPU Intensive operations, such as encoding and decoding.
* Leveraging Go eco-system, such as Kubernetes clients.

## Requirement

* PHP 7.2+
* Go 1.13+
* Swoole 4.4LTS+
* Hyperf 1.1+ (optional)

## Task Delivery Demo

```go
package main

import (
    "github.com/hyperf/gotask/v2/pkg/gotask"
)

type App struct{}

func (a *App) Hi(name string, r *interface{}) error {
    *r = map[string]string{
        "hello": name,
    }
    return nil
}

func main() {
    gotask.SetAddress("127.0.0.1:6001")
    gotask.Register(new(App))
    gotask.Run()
}
```

```php
<?php

use Hyperf\GoTask\IPC\SocketIPCSender;
use function Swoole\Coroutine\run;

require_once "../vendor/autoload.php";

run(function(){
    $task = new SocketIPCSender('127.0.0.1:6001');
    var_dump($task->call("App.Hi", "Hyperf"));
    // [ "hello" => "Hyperf" ]
});

```

## Resources
> English documentation is not yet complete! Please see examples first.

* [Installation](https://github.com/Hyperf/gotask/wiki/Installation-&-Configuration)
* [Document](https://github.com/Hyperf/gotask/wiki/Document)
* [FAQ](https://github.com/Hyperf/gotask/wiki/FAQ)
* [Example](https://github.com/Hyperf/gotask/tree/master/example)
* [Hyperf Example](https://github.com/reasno/gotask-benchmark/blob/master/app/Controller/IndexController.php)

## Benchmark

https://github.com/reasno/gotask-benchmark

## Credit
* https://github.com/spiral/goridge provides the IPC protocol.
* https://github.com/twose helps the creation of this project.







