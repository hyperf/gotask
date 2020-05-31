# GoTask

[English](./README.md) | 中文

[![Build Status](https://travis-ci.org/hyperf/gotask.svg?branch=master)](https://travis-ci.org/hyperf/gotask)

GoTask通过[Swoole进程管理功能](https://wiki.swoole.com/#/process)启动Go进程作为Swoole主进程边车(Sidecar)，利用[进程通讯](https://wiki.swoole.com/#/learn?id=%e4%bb%80%e4%b9%88%e6%98%afipc)将任务投递给边车处理并接收返回值。可以理解为Go版的Swoole TaskWorker。

```bash
composer require hyperf/gotask
```

## 特性

* [超高速低消耗](https://github.com/reasno/gotask-benchmark)
* Co/Socket实现，100%协程化
* 支持Unix Socket、TCP、stdin/stdout管道
* PHP与Go双向通讯
* 边车自动启停
* 支持远程异常捕获
* 支持结构化数据、二进制数据投递
* go边车兼容[net/rpc](https://cloud.tencent.com/developer/section/1143675)
* 自带连接池支持
* 可独立使用，也可深度融合Hyperf

## 使用场景
* 执行阻塞函数，如MongoDB查询
* 执行CPU密集操作，如编码解码
* 接入Go语言生态，如Kubernetes

## 使用要求

* PHP 7.2+
* Go 1.13+
* Swoole 4.4LTS+
* Hyperf 1.1+ (optional)

## 示例

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
    // 打印 [ "hello" => "Hyperf" ]
});

```

## 文档
* [安装与配置](https://github.com/Hyperf/gotask/wiki/Installation-&-Configuration)
* [文档](https://github.com/Hyperf/gotask/wiki/Documentation)
* [FAQ](https://github.com/Hyperf/gotask/wiki/FAQ)
* [示例](https://github.com/Hyperf/gotask/tree/master/example)
* [Hyperf示例](https://github.com/Hyperf/gotask-benchmark/blob/master/app/Controller/IndexController.php)

## Benchmark

https://github.com/reasno/gotask-benchmark

## 鸣谢
* https://github.com/spiral/goridge 提供了IPC通讯的编码和解码。
* https://github.com/twose 提供了人肉答疑支持。



