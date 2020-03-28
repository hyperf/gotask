# GoTask

A lightning speed replacement for Swoole TaskWorker in Go ⚡️

[![Build Status](https://travis-ci.org/Reasno/gotask.svg?branch=master)](https://travis-ci.org/Reasno/gotask)

GoTask通过[Swoole进程管理功能](https://wiki.swoole.com/#/process)启动Go进程作为Swoole主进程边车(Sidecar)，利用[高速进程通讯](https://wiki.swoole.com/#/learn?id=%e4%bb%80%e4%b9%88%e6%98%afipc)将任务投递给边车处理后接收返回值。

```bash
composer require reasno/gotask
```

## 特性 Feature

* 超高速低消耗
* Co/Socket实现，100%协程化
* 支持Unix Socket、TCP、stdin/stdout管道
* 边车自动启停
* 支持远程异常捕获
* 支持结构化数据、二进制数据投递
* go边车兼容[net/rpc](https://cloud.tencent.com/developer/section/1143675)
* 自带连接池支持
* 可独立使用，也可深度融合Hyperf

## 使用场景 Perfect For
* 执行阻塞函数，如MongoDB查询
* 执行CPU密集操作，如机器学习
* 接入Go语言生态，如Kubernetes

## requirement

* PHP 7.2+
* Go 1.13+
* Swoole 4.4LTS+
* Hyperf 1.1+ (optional)

## 消息投递Demo

```go
package main

import (
	"github.com/reasno/gotask/pkg/gotask"
)
// App sample
type App struct{}

// Hi returns greeting message.
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

use Reasno\GoTask\IPC\SocketIPC;
use function Swoole\Coroutine\run;

require_once "../vendor/autoload.php";

run(function(){
    $task = new SocketIPC('127.0.0.1:6001');
    var_dump($task->call("App.Hi", "Reasno"));
    // 打印 [ "hello" => "Reasno" ]
});

```

## 文档

* [文档](https://github.com/Reasno/gotask/wiki)
* [FAQ](https://github.com/Reasno/gotask/wiki/FAQ)
* [示例](https://github.com/Reasno/gotask/tree/master/example)
* [Hyperf示例](https://github.com/Reasno/gotask-benchmark/blob/master/app/Controller/IndexController.php) (实现了连接池，直接按单例注入即可）

## Benchmark

https://github.com/reasno/gotask-benchmark

## 鸣谢
* https://github.com/spiral/goridge 提供了IPC通讯的编码和解码。
* https://github.com/twose 提供了人肉答疑支持。



