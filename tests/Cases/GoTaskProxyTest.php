<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf/GoTask.
 *
 * @link     https://www.github.com/hyperf/gotask
 * @document  https://www.github.com/hyperf/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use Hyperf\GoTask\GoTask;
use HyperfTest\GoProxyStub;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class GoTaskProxyTest extends TestCase
{
    public function testCall()
    {
        $goTask = Mockery::mock(GoTask::class);
        $goTask->shouldReceive('call')->once()->withArgs(['a', 'b', 0])->andReturn('ok');
        $goTask->shouldReceive('call')->once()->withArgs(['GoProxyStub.Test', 'mmm', 1])->andReturn('ok');
        $proxy = new GoProxyStub($goTask);
        $this->assertEquals('ok', $proxy->call('a', 'b', 0));
        $this->assertEquals('ok', $proxy->test('mmm', 1));
    }
}
