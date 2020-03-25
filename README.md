# GoTask

A lightning speed replacement for Swoole TaskWorker in Go ⚡️

## 为什么GoTask

> 在php-fpm的应用中，经常会将一个任务异步投递到Redis等队列中，并在后台启动一些php进程异步地处理这些任务。Swoole提供的TaskWorker是一套更完整的方案，将任务的投递、队列、php任务处理进程管理合为一体。通过底层提供的API可以非常简单地实现异步任务的处理。另外TaskWorker还可以在任务执行完成后，再返回一个结果反馈到Worker。

在Swoole协程普及后，Swoole的TaskWorker一般来说承担三个责任：

1. 遇到CPU密集型的操作，扔进来。
2. 遇到暂时无法协程化的IO操作（如MongoDB），扔进来。
3. 遇到某些组件不支持协程，扔进来。

前两条TaskWorker能做的，Go都可以做的更好。第三条嘛，虽然放弃了PHP生态比较遗憾，但是可以接入Go生态也不错。

GoTask提供了与Swoole TaskWorker非常接近的使用体验，目标是在一些场景下取代TaskWorker，直接用Go做Swoole的有力补充。

## 消息投递Demo

```go
package main

import (
	"github.com/reasno/gotask/pkg/gotask"
)
// App sample
type App struct{}

// Hi returns greeting message.
func (a *App) Hi(name interface{}, r *interface{}) error {
	*r = map[string]interface{}{
		"hello": name,
	}
	return nil
}

func main() {
	gotask.Register(new(App))
	gotask.Run()
}
```

```php
<?php

use Reasno\GoTask\Relay\CoroutineSocketRelay;
use Spiral\Goridge\RPC;
use function Swoole\Coroutine\run;

require_once "../vendor/autoload.php";

run(function(){
    $task = new RPC(
        new CoroutineSocketRelay("127.0.0.1", 6001)
    );
    var_dump($task->call("App.Hi", "Reasno"));
    // 打印 [ "hello" => "Reasno" ]
});

```

在src/example里可以跑这个示例。

## 快速体验

```bash
composer require reasno/gotask
```

如果是Hyperf用户，可以直接

```bash
php bin/hyperf.php vendor:publish
```

导出Go初始模版和Hyperf配置文件。

在项目根目录执行构建：

```bash
cd gotask
go build cmd/app.go
```

然后按照正常流程启动Hyperf即可，`ps -ef | grep hyperf`会发现Go进程随Hyperf一起启动了。
当我们的Hyperf主进程退出时，Go进程也会随之退出，使用体验完全模仿TaskWorker。

在Hyperf中向GoTask投递任务：

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Reasno\GoTask\GoTask;

class IndexController extends AbstractController
{
    /**
     * @return array
     */
    public function index(GoTask $task)
    {
        return $task->call('App.Hi', ['Swoole is Awesome,', 'So is Go!']);
    }
}
```

### FAQ

Q: 投递性能如何？

A：采用与TaskWorker本身完全一致的跨进程通讯投递，默认Unix Socket，也支持TCP。
根据Swoole的计算，100 万次通信仅需 1.02 秒。而到了Worker上以后，显然Go的速度只会比PHP更快，也没有阻塞函数的担忧。
未来会增加一些Benchmark。目前比较想测试的一个场景是，用Go来读取MongoDB，PHP来调用Go。

Q：和RPC调用Go服务有什么区别？

A：首先，IPC性能更强劲。其次结构上，Go完全是PHP的边车，生命周期全由PHP控制，没有分布式烦恼。

Q：为什么不直接用Go写整个服务？

A：为什么不让同桌帮我做作业？

Q：Go一定要写到PHP项目当中吗？

A：不是的，Go项目可以分离出去独立部署，改一下配置文件就行了。

## 初步测试

```
ab -k -c 100 -n 10000 http://localhost:9501/
```

macbook pro上执行上面的IPC例子，可以看到IPC后和裸跑hello world性能非常接近。

直接HelloWorld，不投递GoTask：

```
Server Software:        Hyperf
Server Hostname:        localhost
Server Port:            9501

Document Path:          /
Document Length:        11 bytes

Concurrency Level:      100
Time taken for tests:   0.480 seconds
Complete requests:      10000
Failed requests:        0
Keep-Alive requests:    10000
Total transferred:      1530000 bytes
HTML transferred:       110000 bytes
Requests per second:    20834.20 [#/sec] (mean)
Time per request:       4.800 [ms] (mean)
Time per request:       0.048 [ms] (mean, across all concurrent requests)
Transfer rate:          3112.92 [Kbytes/sec] received
```

投递GoTask：

```
Server Software:        Hyperf
Server Hostname:        localhost
Server Port:            9501

Document Path:          /
Document Length:        11 bytes

Concurrency Level:      100
Time taken for tests:   0.600 seconds
Complete requests:      10000
Failed requests:        1564
   (Connect: 0, Receive: 0, Length: 1564, Exceptions: 0)
Keep-Alive requests:    10000
Total transferred:      1591564 bytes
HTML transferred:       111564 bytes
Requests per second:    16658.34 [#/sec] (mean)
Time per request:       6.003 [ms] (mean)
Time per request:       0.060 [ms] (mean, across all concurrent requests)
Transfer rate:          2589.14 [Kbytes/sec] received
```

